<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserPicturesRequest;
use App\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserPicturesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(): View
    {
        $user = auth()->user();
        $pictures = $user->pictures()->orderBy('order')->orderBy('created_at')->get();

        return view('pictures', [
            'user' => $user,
            'pictures' => $pictures
        ]);
    }

    public function addPictures(AddUserPicturesRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $maxOrder = $user->pictures()->max('order') ?? 0;

        if ($request->hasFile('picture')) {
            foreach ($request->file('picture') as $index => $picture) {
                Picture::create([
                    'user_id' => $user->id,
                    'path' => $picture->store('profilePictures', 'public'),
                    'order' => $maxOrder + $index + 1
                ]);
            }
            return redirect()
                ->back();
        }
        return redirect()
            ->back();
    }

    public function reorderPictures(Request $request): JsonResponse
    {
        $request->validate([
            'picture_ids' => 'required|array',
            'picture_ids.*' => 'exists:pictures,id'
        ]);

        $user = auth()->user();
        
        foreach ($request->picture_ids as $order => $pictureId) {
            $picture = Picture::findOrFail($pictureId);
            // Verify picture belongs to user
            if ($picture->user_id != $user->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $picture->update(['order' => $order + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function destroyPicture(int $id): RedirectResponse
    {
        $picture = Picture::find($id);
        $picture->delete();

        return redirect()->back();
    }
}
