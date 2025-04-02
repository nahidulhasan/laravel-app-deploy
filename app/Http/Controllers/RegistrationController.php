<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\User;
use App\Services\UserRoleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RegistrationController extends Controller
{
    protected $service;

    /**
     * contructor method
     *
     * @param UserRoleService $service
     */
    public function __construct(UserRoleService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function insertDummy()
    {
        // Create a user with a profile
        $user = User::create(['username' => 'mamun', 'name' => 'Mamun', 'email' => 'mamun@example.com']);
        $user->profile()->create(['bio' => 'Software Engineer']);

        // Create a post for a user
        $post = $user->posts()->create(['title' => 'My First Post', 'content' => 'This is a Laravel tutorial.']);

        // Attach tags to a post
        $tag1 = Tag::create(['name' => 'Laravel']);
        $tag2 = Tag::create(['name' => 'PHP']);
        $post->tags()->attach([$tag1->id, $tag2->id]);

        // Add a comment to a post
        $post->comments()->create(['comment' => 'Great post!']);
    }


    public function retrieveDummy()
    {
        // Get user with profile
        $user = User::with('profile')->find(1);
        echo $user->profile->bio;

        // Get user with posts
                $user = User::with('posts')->find(1);
                foreach ($user->posts as $post) {
                    echo $post->title;
                }

        // Get posts with tags
                $posts = Post::with('tags')->get();
                foreach ($posts as $post) {
                    echo $post->title . ' has tags: ';
                    foreach ($post->tags as $tag) {
                        echo $tag->name . ', ';
                    }
                }

        // Get comments of a user through posts
                $userComments = User::find(1)->comments;
                foreach ($userComments as $comment) {
                    echo $comment->comment;
                }

    }


    public function updateDummy()
    {
        // Update profile bio
        $user = User::find(1);
        $user->profile->update(['bio' => 'Senior Software Engineer']);

        // Update a post title
        $post = Post::find(1);
        $post->update(['title' => 'Updated Laravel Post']);

       // Sync tags (replace existing tags)
        $post->tags()->sync([1, 3]); // Assuming tag IDs 1 and 3 exist

    }


    public function deleteDummy(){
        // Delete a profile (User remains)
        $user = User::find(1);
        $user->profile()->delete();

       // Delete a post and its comments
        $post = Post::find(1);
        $post->delete(); // Also deletes comments due to cascade

       // Detach a tag from a post
        $post->tags()->detach(1); // Remove tag ID 1
    }



    /**
     *  index data
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $data = $this->service->index($request->all());
        return view('user.index', ['data' => $data]);
    }

    public function create()
    {
        $method = 'post';
        $header = 'Create User Role';
        $formUrl = route('user-role-store');
        $formData = $this->service->getFormData();
        return view('user.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function edit($id)
    {
        $method = 'put';
        $header = 'Edit User Role';
        $formUrl = route('user-role-update', $id);
        $formData = $this->service->getFormData($id);
        return view('user.create-edit', compact('method', 'header', 'formUrl', 'formData'));
    }

    public function store(Request $request)
    {
        try {
            $this->service->store($request->all());
            $request->session()->flash('success', 'Entry Created Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $this->service->update($request->all(), $id);
            $request->session()->flash('success', 'Entry Updated Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function import()
    {
        // replace old data with new data
    }
    /**
     * user role entry delation
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function destroy(Request $request, $id)
    {
        try {
            $this->service->delete($id);
            $request->session()->flash('success', 'Entry Deleted Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }

    public function sync(Request $request)
    {
        try {
            $this->service->sync();
            $request->session()->flash('success', 'Sync Completed Successfully !');
            return redirect()->action([UserRolesController::class, 'index']);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            $request->session()->flash('error', 'Something Went Wrong !');
            return redirect()->action([UserRolesController::class, 'index']);
        }
    }
}
