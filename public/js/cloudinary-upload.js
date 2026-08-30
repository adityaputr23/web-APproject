/**
 * Cloudinary Chunked Upload Helper
 * Uploads large files (images & videos) in 6MB chunks using the
 * Cloudinary resumable upload API (X-Unique-Upload-Id + Content-Range).
 *
 * Usage:
 *   cloudinaryChunkedUpload(sig, file, {
 *     onProgress: (percent, loadedMB, totalMB) => {},
 *     onSuccess:  (secureUrl) => {},
 *     onError:    (errorMessage) => {},
 *   });
 */
window.cloudinaryChunkedUpload = function(sig, file, callbacks) {
    const CHUNK_SIZE = 6 * 1024 * 1024; // 6 MB per chunk
    const totalSize  = file.size;
    const totalChunks = Math.ceil(totalSize / CHUNK_SIZE);

    // Generate a unique ID for this upload session
    const uniqueUploadId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
    });

    const endpoint = `https://api.cloudinary.com/v1_1/${sig.cloud_name}/auto/upload`;

    let currentChunk = 0;
    let totalUploaded = 0;

    function uploadChunk() {
        const start = currentChunk * CHUNK_SIZE;
        const end   = Math.min(start + CHUNK_SIZE, totalSize);
        const chunk = file.slice(start, end);

        const formData = new FormData();
        formData.append('file', chunk);
        formData.append('api_key',   sig.api_key);
        formData.append('timestamp', sig.timestamp);
        formData.append('signature', sig.signature);
        formData.append('folder',    sig.folder);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', endpoint, true);
        xhr.setRequestHeader('X-Unique-Upload-Id', uniqueUploadId);
        xhr.setRequestHeader('Content-Range', `bytes ${start}-${end - 1}/${totalSize}`);

        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const chunkUploaded = totalUploaded + event.loaded;
                const percent  = Math.min(Math.round((chunkUploaded / totalSize) * 100), 99);
                const loadedMB = (chunkUploaded / 1024 / 1024).toFixed(1);
                const totalMB  = (totalSize / 1024 / 1024).toFixed(1);
                if (callbacks.onProgress) {
                    callbacks.onProgress(percent, loadedMB, totalMB);
                }
            }
        };

        xhr.onload = function() {
            if (xhr.status === 200 || xhr.status === 201) {
                // Final chunk — Cloudinary returns the full result
                try {
                    const result = JSON.parse(xhr.responseText);
                    if (result.secure_url) {
                        if (callbacks.onProgress) callbacks.onProgress(100, (totalSize/1024/1024).toFixed(1), (totalSize/1024/1024).toFixed(1));
                        if (callbacks.onSuccess) callbacks.onSuccess(result.secure_url);
                        return;
                    }
                } catch(e) {}
                if (callbacks.onError) callbacks.onError('Cloudinary tidak mengembalikan URL file.');
            } else if (xhr.status === 206) {
                // Chunk accepted — move to next chunk
                totalUploaded += (end - start);
                currentChunk++;
                uploadChunk();
            } else {
                // Error response
                let errMsg = `Upload gagal (status ${xhr.status}).`;
                try {
                    const errData = JSON.parse(xhr.responseText);
                    if (errData.error && errData.error.message) {
                        errMsg = errData.error.message;
                    }
                } catch(e) {}
                if (callbacks.onError) callbacks.onError(errMsg);
            }
        };

        xhr.onerror = function() {
            if (callbacks.onError) {
                callbacks.onError('Koneksi terputus saat mengupload chunk ke Cloudinary. Coba lagi.');
            }
        };

        xhr.send(formData);
    }

    // Start chunked upload
    uploadChunk();
};
