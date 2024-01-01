<?php

namespace App\Http\Controllers\V1;

// use App\Http\Resources\V1\ImageManipulationResource;
use App\Models\Album;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ImageManipulation;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Http\Resources\V1\ImageManipulationResource;
use App\Http\Requests\ResizeImageManipulationRequest;
use Response;

class ImageManipulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return ImageManipulationResource::collection(ImageManipulation::where('user_id', $request->user()->id)->paginate());

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageManipulation  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, ImageManipulation $image): ImageManipulationResource
    {
        if ($request->user()->id != $image->user_id) {
            return abort(403, "Unauthorized");
        }
        return new ImageManipulationResource($image);
        // return ImageManipulationResource::collection(ImageManipulation::where("id", $imageManipulation->id)->paginate());
    }
    public function byAlbum(Request $request, Album $album)
    {
        if ($request->user()->id != $album->user_id) {
            return abort(403, "Unauthorized");
        }

        $where = [
            'album_id' => $album->id,
        ];
        return ImageManipulationResource::collection(ImageManipulation::where($where)->paginate());
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageManipulation  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ImageManipulation $image)
    {
        if ($request->user()->id != $image->user_id) {
            return abort(403, "Unauthorized");
        }

        $image->delete();

        return response('', 204);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ResizeImageManipulationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resize(ResizeImageManipulationRequest $request)
    {

        $all = $request->all();
        $image = $all['image'];
        unset($all['image']); // Remove the image from $all


        // // Data to save in DB
        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => $request->user()->id,
        ];


        // // Check if album_id does exist in DB
        if (isset($all['album_id'])) {
            $album = Album::find($all['album_id']);

            if ($request->user()->id != $album->user_id) {
                return abort(403, 'Unauthorized');
            }
            $data['album_id'] = $all['album_id'];
        }
        // images/dash2j3da/test.jpg
        // images/dash2j3da/test-resized.jpg
        $dir = 'images/' . Str::random() . '/'; // Make a new directory in the public folder
        // Directory path
        $absolutePath = public_path($dir);
        File::makeDirectory($absolutePath);
        if ($image instanceof UploadedFile) {
            $data['name'] = $image->getClientOriginalName(); // Image Name With Extension
            // test.jpg -> test-resized.jpg
            $filename = pathinfo($data['name'], PATHINFO_FILENAME); // Image Name Without Extension
            $extension = pathinfo($data['name'], PATHINFO_EXTENSION); // Image Extension
            $originalPath = $absolutePath . $data['name'];
            $image->move($absolutePath, $data['name']); // Move the image to the public folder
        } else {
            $data['name'] = pathinfo($image, PATHINFO_BASENAME);
            $filename = pathinfo($data['name'], PATHINFO_FILENAME);
            $extension = pathinfo($data['name'], PATHINFO_EXTENSION);
            $originalPath = $absolutePath . $data['name'];
            copy($image, $originalPath);
        }

        $data['path'] = $dir . $data['name'];

        // Width
        $w = $all['w'];
        // Height
        $h = $all['h'] ?? false;

        list($width, $height, $image) = $this->getImageWidthAndHeight($w, $h, $originalPath);

        // Resize the image
        $resizedFilename = $filename . '-resized.' . $extension;
        $image->resize($width, $height)->save($absolutePath . $resizedFilename);
        $data['output_path'] = $dir . $resizedFilename;

        $imageManipulation = ImageManipulation::create($data);

        return new ImageManipulationResource($imageManipulation);
    }

    protected function getImageWidthAndHeight($w, $h, string $originalPath)
    {

        // 1000 - 50% => 500px

        // Create an instance of the image
        $image = Image::make($originalPath);
        // Get the original width and height
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if (str_ends_with($w, '%')) {

            $ratioW = (float) str_replace('%', '', $w);
            $ratioH = $h ? (float) str_replace('%', '', $h) : $ratioW;

            $newWidth = $originalWidth * $ratioW / 100;
            $newHeight = $originalHeight * $ratioH / 100;

        } else {
            $newWidth = (float) $w;
            /**
             *  $originalWidth - $newWidth
             *  $originalHeight - $newHeight
             *  ----------------------------
             *  $newHeight = $originalHeight * $newWidth/$originalWidth
             */
            $newHeight = $h ? (float) $h : $originalHeight * $newWidth / $originalWidth;
        }
        return [$newWidth, $newHeight, $image];
    }

}

