<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function index(Request $request)
    {
        $isHTMX = $request->hasHeader('HX-Request');

        $chapters = Chapter::orderBy('order')->get();
        return view('outline.chapters.index', compact('chapters', 'isHTMX'))->fragmentIf($isHTMX, 'chapter-list');
    }

    public function show(Request $request, Chapter $chapter)
    {
        $isHTMX = $request->hasHeader('HX-Request');

        return view('outline.chapters.show', compact('chapter'))->fragmentIf($isHTMX, 'chapter-details');
    }

    public function create(Request $request)
    {
        $isHTMX = $request->hasHeader('HX-Request');

        return view('outline.chapters.create')->fragmentIf($isHTMX, 'create-chapter-form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $nextOrder = (Chapter::max('order') ?? 0) + 1;
        $data['order'] = $nextOrder;

        $chapter = Chapter::create($data);

        return redirect()->route('outline.chapters.show', $chapter);
    }

    public function edit(Chapter $chapter)
    {
        return view('outline.chapters.edit', compact('chapter'));
    }

    public function update(Request $request, Chapter $chapter)
    {
        $data = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'order' => 'nullable|integer',
        ]);

        $chapter->update($data);

        return redirect()->route('outline.chapters.show', $chapter);
    }

    public function destroy(Chapter $chapter)
    {
        $deletedOrder = $chapter->order;

        $chapter->delete();

        Chapter::where('order', '>', $deletedOrder)->decrement('order');
        
        return redirect()->route('outline.chapters.index');
    }

}
