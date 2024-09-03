<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        // Get all tags from the request
        $tags = $request->input('tags', []);

        // Check if any tags were provided
        if (!empty($tags)) {
            // Look through tags in links table and get any records that match provided tags
            $links = Link::where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->whereJsonContains('tags', $tag);
                }
            })->get();
        } else {
            // Get all tags
            $links = Link::all();
        }

        return response()->json($links);
    }
}
