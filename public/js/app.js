document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navbar = document.querySelector('.navbar');

    if (mobileToggle && navMenu) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navMenu.classList.toggle('open');
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                if (navMenu.classList.contains('open')) {
                    icon.className = 'ri-close-line';
                } else {
                    icon.className = 'ri-menu-line';
                }
            }
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navMenu.classList.contains('open') && !navbar.contains(e.target)) {
                navMenu.classList.remove('open');
                const icon = mobileToggle.querySelector('i');
                if (icon) icon.className = 'ri-menu-line';
            }
        });
    }

    // 2. Smooth Scrolling & Active State
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('section');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (window.scrollY >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });

    // Close mobile menu when a link is clicked
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (navMenu) navMenu.classList.remove('open');
            if (mobileToggle) {
                const icon = mobileToggle.querySelector('i');
                if (icon) icon.className = 'ri-menu-line';
            }
        });
    });

    // 3. Showcase Tab Switcher (Scoped per section container)
    function switchShowcaseTab(tabBtn) {
        const section = tabBtn.closest('.showcase-section, .dev-projects-section') || document;
        const tabName = tabBtn.getAttribute('data-tab');

        const showcaseTabs = section.querySelectorAll('.showcase-tab');
        const showcasePanels = section.querySelectorAll('.showcase-tab-panel');

        showcaseTabs.forEach(tab => {
            if (tab.getAttribute('data-tab') === tabName) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        showcasePanels.forEach(panel => {
            if (panel.getAttribute('data-panel') === tabName) {
                panel.classList.add('active');
            } else {
                panel.classList.remove('active');
            }
        });
    }

    document.addEventListener('click', (e) => {
        const tabBtn = e.target.closest('.showcase-tab');
        if (tabBtn) {
            e.preventDefault();
            switchShowcaseTab(tabBtn);
        }
    });

    // Navbar links with data-set-filter scroll to showcase and activate tab
    document.querySelectorAll('[data-set-filter]').forEach(link => {
        link.addEventListener('click', (e) => {
            const filter = link.getAttribute('data-set-filter');
            const targetTab = (filter === 'video' || filter === 'photography') ? 'video' : 'photo';
            switchShowcaseTab(targetTab);
        });
    });

    // Helper: Google Drive ID extraction
    function getGoogleDriveId(url) {
        if (!url) return null;
        const match = url.match(/(?:drive\.google\.com\/(?:file\/d\/|open\?id=|uc\?(?:.*&)?id=)|drive\.usercontent\.google\.com\/download\?id=)([\w-]+)/i);
        return match ? match[1] : null;
    }

    // Helper: Google Drive streamable MP4 URL
    function getGoogleDriveStreamUrl(url) {
        const id = getGoogleDriveId(url);
        return id ? `https://drive.usercontent.google.com/download?id=${id}&confirm=t` : null;
    }

    // Helper: Convert YouTube/Vimeo URL to embed URL
    function toEmbedUrl(url) {
        if (!url) return null;
        const ytMatch = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([\w-]{11})/i);
        if (ytMatch) return `https://www.youtube.com/embed/${ytMatch[1]}?autoplay=1&rel=0`;

        const viMatch = url.match(/vimeo\.com\/(?:video\/)?(\d+)/i);
        if (viMatch) return `https://player.vimeo.com/video/${viMatch[1]}?autoplay=1`;

        return null;
    }

    // 4. Project Lightbox Modal
    const modal = document.getElementById('projectModal');
    const modalClose = document.querySelector('.modal-close');

    if (modal && modalClose) {
        const modalContent = document.getElementById('modalContent');

        document.addEventListener('click', (e) => {
            const item = e.target.closest('.showcase-item');
            if (!item) return;

            const title    = item.getAttribute('data-title');
            const desc     = item.getAttribute('data-desc');
            const category = item.getAttribute('data-category');
            const tags     = item.getAttribute('data-tags') ? item.getAttribute('data-tags').split(',') : [];
            const type     = item.getAttribute('data-type');
            const asset    = item.getAttribute('data-asset');
            let   url      = item.getAttribute('data-url');

            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDesc').textContent = desc;
            document.getElementById('modalCategory').textContent = category;

            const modalImg    = document.getElementById('modalImg');
            const modalVideo  = document.getElementById('modalVideo');
            const modalIframe = document.getElementById('modalIframe');

            const driveId   = getGoogleDriveId(asset) || getGoogleDriveId(url);
            const streamUrl = item.getAttribute('data-stream-url') || (driveId ? `https://drive.usercontent.google.com/download?id=${driveId}&confirm=t` : null);
            const embedUrl  = item.getAttribute('data-embed-url') || toEmbedUrl(asset) || toEmbedUrl(url);
            const isVideo   = type === 'video' || Boolean(streamUrl) || Boolean(embedUrl) || Boolean(asset && asset.match(/\.(mp4|webm|ogg|mov|avi|mkv|flv|wmv|m4v)$/i));

            // Reset layout classes
            if (modalContent) {
                modalContent.classList.remove('is-landscape-video', 'is-portrait-video', 'is-landscape-photo', 'is-portrait-photo', 'is-video');
            }

            if (streamUrl || (isVideo && !embedUrl)) {
                // Direct HTML5 video streaming (MP4 files or Google Drive raw stream)
                const videoSource = streamUrl || asset;
                if (modalImg) modalImg.classList.add('hide');
                if (modalIframe) {
                    modalIframe.classList.add('hide');
                    modalIframe.src = '';
                }
                if (modalVideo) {
                    modalVideo.classList.remove('hide');
                    modalVideo.src = videoSource;
                    modalVideo.load();

                    const applyVideoLayout = () => {
                        const w = modalVideo.videoWidth;
                        const h = modalVideo.videoHeight;
                        if (w && h && modalContent) {
                            const ratio = w / h;
                            modalContent.classList.remove('is-landscape-video', 'is-portrait-video');
                            if (ratio < 0.9) {
                                modalContent.classList.add('is-portrait-video'); // 9:16 vertical video player
                            } else {
                                modalContent.classList.add('is-landscape-video'); // 16:9 widescreen video player
                            }
                        }
                    };

                    modalVideo.onloadedmetadata = applyVideoLayout;
                    if (modalVideo.videoWidth) applyVideoLayout();
                    modalVideo.play().catch(() => {});

                    modalVideo.onerror = () => {
                        // Fallback to Google Drive iframe if direct stream is blocked
                        if (driveId && modalIframe) {
                            console.warn('Direct video streaming blocked, falling back to Google Drive preview iframe');
                            modalVideo.classList.add('hide');
                            modalIframe.classList.remove('hide');
                            modalIframe.src = `https://drive.google.com/file/d/${driveId}/preview`;
                        }
                    };
                }
                if (modalContent) {
                    modalContent.classList.add('is-landscape-video');
                }
                // Pause all autoplay grid videos to free resources
                document.querySelectorAll('.showcase-video').forEach(v => v.pause());
            } else if (embedUrl) {
                // YouTube or Vimeo iframe embed
                if (modalImg) modalImg.classList.add('hide');
                if (modalVideo) {
                    modalVideo.classList.add('hide');
                    modalVideo.pause();
                    modalVideo.src = '';
                }
                if (modalIframe) {
                    modalIframe.classList.remove('hide');
                    modalIframe.src = embedUrl;
                }
                if (modalContent) {
                    modalContent.classList.add('is-landscape-video');
                }
                // Pause all autoplay grid videos to free resources
                document.querySelectorAll('.showcase-video').forEach(v => v.pause());
            } else {
                // Static image view
                if (modalVideo) {
                    modalVideo.classList.add('hide');
                    modalVideo.pause();
                    modalVideo.src = '';
                }
                if (modalIframe) {
                    modalIframe.classList.add('hide');
                    modalIframe.src = '';
                }
                if (modalImg) {
                    modalImg.classList.remove('hide');
                    modalImg.src = asset;
                    modalImg.alt = title;

                    const applyImgLayout = () => {
                        const w = modalImg.naturalWidth;
                        const h = modalImg.naturalHeight;
                        if (w && h && modalContent) {
                            const ratio = w / h;
                            modalContent.classList.remove('is-landscape-photo', 'is-portrait-photo');
                            if (ratio < 0.9) {
                                modalContent.classList.add('is-portrait-photo');
                            } else {
                                modalContent.classList.add('is-landscape-photo');
                            }
                        }
                    };

                    modalImg.onload = applyImgLayout;
                    if (modalImg.complete && modalImg.naturalWidth) applyImgLayout();
                }
            }

            const tagsContainer = document.getElementById('modalTags');
            tagsContainer.innerHTML = '';
            tags.forEach(tag => {
                const span = document.createElement('span');
                span.className = 'mini-tag';
                span.textContent = tag.trim();
                tagsContainer.appendChild(span);
            });

            // Action button (External link or Google Drive link)
            const actionBtn = document.getElementById('modalUrl');
            if (!url && driveId) {
                url = `https://drive.google.com/file/d/${driveId}/view`;
            }

            if (url && url !== '#' && url !== '' && url !== 'null') {
                actionBtn.href = url;
                actionBtn.style.display = 'inline-flex';
                if (driveId) {
                    actionBtn.innerHTML = '<i class="ri-external-link-line"></i> Buka di Google Drive';
                } else {
                    actionBtn.innerHTML = '<i class="ri-external-link-line"></i> View Project';
                }
            } else {
                actionBtn.style.display = 'none';
            }

            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        });

        const closeModal = () => {
            modal.classList.remove('open');
            document.body.style.overflow = '';
            const modalVideo = document.getElementById('modalVideo');
            if (modalVideo) {
                modalVideo.pause();
                modalVideo.src = '';
            }
            const modalIframe = document.getElementById('modalIframe');
            if (modalIframe) {
                modalIframe.src = '';
            }
            // Resume grid autoplay videos
            document.querySelectorAll('.showcase-video').forEach(v => {
                if (!v.closest('.hidden-item')) v.play().catch(() => {});
            });
        };

        modalClose.addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // ESC key closes modal
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
        });
    }

    // 4. Contact Form AJAX Submission
    const contactForm = document.getElementById('contactForm');
    const alertBox = document.getElementById('formAlert');
    const submitBtn = contactForm ? contactForm.querySelector('button[type="submit"]') : null;

    if (contactForm && alertBox && submitBtn) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.spinner');

            // Show loading state
            btnText.classList.add('hide');
            spinner.classList.remove('hide');
            submitBtn.disabled = true;

            alertBox.className = 'form-alert hide';
            alertBox.textContent = '';

            const formData = new FormData(contactForm);

            try {
                const response = await fetch(contactForm.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    alertBox.className = 'form-alert success';
                    alertBox.textContent = data.message;
                    contactForm.reset();
                } else {
                    alertBox.className = 'form-alert error';
                    alertBox.textContent = data.message || 'An error occurred. Please check the inputs.';
                }
            } catch (err) {
                alertBox.className = 'form-alert error';
                alertBox.textContent = 'Network error. Please try again later.';
            } finally {
                // Reset button state
                btnText.classList.remove('hide');
                spinner.classList.add('hide');
                submitBtn.disabled = false;
            }
        });
    }
});
