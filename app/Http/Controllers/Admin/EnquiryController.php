<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use Illuminate\Http\Request;

class EnquiryController extends Controller
{
    /**
     * Display a listing of enquiries.
     */
    public function index()
    {
        $enquiries = Enquiry::orderBy('created_at', 'desc')->get();
        return view('admin.enquiries.index', compact('enquiries'));
    }

    /**
     * Toggle the read state of an enquiry.
     */
    public function toggleRead(Enquiry $enquiry)
    {
        $enquiry->update([
            'is_read' => !$enquiry->is_read
        ]);

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Enquiry status updated.');
    }

    /**
     * Remove the specified enquiry from storage.
     */
    public function destroy(Enquiry $enquiry)
    {
        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('success', 'Enquiry deleted successfully.');
    }
}
