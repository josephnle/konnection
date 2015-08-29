<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\View\View;

use Imagine\Image\Point;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;

use File;

class UsersController extends Controller {

    /**
     * Returns HTML markup for search results in users search form.
     *
     * @return \Illuminate\View\View
     */
    public function search()
    {
        $input = Input::get('input');

        // Populates search results of users with first and last names similar to user input.
        $user_results = User::where('first_name', 'LIKE', '%' . $input . '%')
                            ->orWhere('last_name', 'LIKE', '%' . $input . '%')
                            ->get();

        return view('search.users', compact('user_results'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit()
    {
        $user = \Auth::user();
        return view('pages.settings.account', compact('user'));
    }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update(UpdateUserRequest $req)
	{
        // Only take acceptable input
		$input = $req->only('avatar', 'password', 'email', 'first_name', 'last_name');

        // Update user
        \Auth::user()->update($input);

        if (!is_null($input['avatar'])) $this->cropAvatar();

        return redirect()->action('UsersController@edit')
            ->with('message', 'Your profile has been updated.');
	}

    /**
     * Crops avatar image to square and corrects orientation.
     *
     * @return void
     */
    private function cropAvatar() {

        // Image manipulation class part of codesleeve/laravel-stapler.
        $imagine = new Imagine();

        // Gets full path of uploaded avatar, creates Imagine object to manipulate image.
        $avatarPath = \Auth::user()->avatar->url();
        $image =  $imagine->open(public_path() . $avatarPath);

        // Native EXIF read function to determine dimension and orientations.
        $exifData = exif_read_data(public_path() . $avatarPath);
        $width = $exifData['ExifImageWidth'];
        $height = $exifData['ExifImageLength'];

        // Crops off top and  bottom for tall images.
        if ($height > $width) {
            $start = ($height - $width) / 2;
            $image->crop(new Point(0, $start), new Box($width, $width));
        }

        // Crops off sides for wide images.
        else {
            $start = ($width - $height) / 2;
            $image->crop(new Point($start, 0), new Box($height, $height));
        }

        // Adjusts orientation depending on EXIF orientation data.
        switch($exifData['Orientation']) {
            case 3:
                $image->rotate(180);
                break;
            case 6:
                $image->rotate(90);
                break;
            case 8:
                $image->rotate(-90);
                break;
        }

        // Replaces original avatar and ensures previous image is deleted.
        File::delete(public_path() . $avatarPath);
        $image->save(public_path() . $avatarPath);
    }
}
