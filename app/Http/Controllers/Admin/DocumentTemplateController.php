<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentTemplate;
use Illuminate\Http\Request;

class DocumentTemplateController extends Controller
{
    public function editAuthorization()
    {
        if (!auth()->user()->can('document-template-table')) {
            abort(403);
        }

        $document = DocumentTemplate::forType('authorization');

        return view('admin.sihati.documents.edit', compact('document'));
    }

    public function updateAuthorization(Request $request)
    {
        if (!auth()->user()->can('document-template-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DocumentTemplate::forType('authorization')->update($data + ['updated_by' => auth('admin')->id()]);

        return redirect()->route('admin.sihati.documents.authorization')
            ->with('success', 'تم حفظ وثيقة التفويض بنجاح');
    }

    public function editPledge()
    {
        if (!auth()->user()->can('document-template-table')) {
            abort(403);
        }

        $document = DocumentTemplate::forType('pledge');

        return view('admin.sihati.documents.edit', compact('document'));
    }

    public function updatePledge(Request $request)
    {
        if (!auth()->user()->can('document-template-edit')) {
            abort(403);
        }

        $data = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DocumentTemplate::forType('pledge')->update($data + ['updated_by' => auth('admin')->id()]);

        return redirect()->route('admin.sihati.documents.pledge')
            ->with('success', 'تم حفظ وثيقة التعهد بنجاح');
    }
}
