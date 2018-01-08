<?php

namespace App\Repositories;

use Bosnadev\Repositories\Contracts\RepositoryInterface;
use Bosnadev\Repositories\Eloquent\Repository;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Helpers\ImageHelper;

class BaseRepository extends Repository implements RepositoryInterface
{
    public $errors;

    public function model()
    {
    }

    /**
     * Find resource or throw NotFoundHttpException
     *
     * @param $id
     * @param array $columns
     *
     * @throws NotFoundHttpException
     * @return mixed
     */
    public function findOrFail($id, $columns = ['*'])
    {
        $found = $this->find($id, $columns);

        if (!$found) {
            throw new NotFoundHttpException;
        }

        return $found;
    }

    /**
     * Find first resource or create new
     *
     * @param $params
     *
     * @return mixed
     */
    public function firstOrNew($params)
    {
        return $this->model->firstOrNew($params);
    }

    /**
     * Check if resource was updated since $since param
     *
     * @param integer $conferenceId
     * @param Carbon $since date from If-Modified-Since header
     * @param array $params
     *
     * @return bool
     */
    public function checkLastUpdate($conferenceId, $since, $params = [])
    {
        $data = $this->findByConference($conferenceId)->withTrashed();

        if ($since) {
            $data = $data->where('updated_at', '>=', $since->toDateTimeString());
        }

        if ($params) {
            $data = $data->where($params);
        }

        $data = $data->withTrashed()->first();

        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Resize and Save image
     *
     * @param $image
     * @param string $directory
     * @param array $size
     *
     * @return string
     */
    public function saveImage($image, $directory = '', $size = ['width' => 800, 'height' => 600])
    {
        return ImageHelper::saveImage($image, $directory,  $size);
    }

    /**
     * Delete image by path
     *
     * @param string $path
     *
     * @return mixed
     */
    public function deleteImage($path)
    {
        return ImageHelper::deleteImage($path);
    }

    /**
     * Find or create
     *
     * @param array $parameters
     *
     * @return mixed
     */
    public function firstOrCreate(array $parameters = [])
    {
        return $this->model->firstOrCreate($parameters);
    }

    /**
     * Find items by conference
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function findByConference($id)
    {
        return  $this->model->where('conference_id', $id);
    }
}
