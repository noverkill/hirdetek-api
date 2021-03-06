<?php
namespace hirdetek\V1\Rest\Hirdetes;

use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

class HirdetesResource extends AbstractResourceListener
{
    protected $mapper;

    public function __construct($mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {

        $user = $this->getIdentity()->getAuthenticationIdentity();

        $request = $this->getEvent()->getRequest();

        $id = $request->getQuery('id');

        $kod = $request->getQuery('kod');

        $files = $request->getfiles();

        $filename = '';

        $upload_dir = "./public/upload/";

        $time = time();

        $folder_name = date('Y', $time) . '/' . date('m', $time) . '/' . date('d', $time) . '/';

        // todo: elvileg barki fel tud tolteni kepet olyan hirdeteshez amit nem regisztralt felhasznalo csinalt!!!
        //       meg azt lehetne csinalni hogy ha nincs user akkor keljen a hirdetes aktivalo kodja es azt lehetne ellenorizni
        if($files->count() > 0  && $id /*&& $user*/) {

            foreach ($files as $files) {

                $filename = str_replace(' ', '.', microtime()) . "_" . substr($files['name'], -100);
            }
        }

        return $this->mapper->create($data, $user, $id, $filename, $folder_name, $upload_dir, $files, $kod);

        //return new ApiProblem(405, 'The POST method has not been defined');
    }

    /**
     * Delete a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function delete($id)
    {

        $user = $this->getIdentity()->getAuthenticationIdentity();

        //print_r($user);

        if(! $user) return ApiProblem(401, 'Unauthorized');

        $email = $user['user_id'];

        return $this->mapper->delete($id, $email);
        //return new ApiProblem(405, 'The DELETE method has not been defined for individual resources');
    }

    /**
     * Delete a collection, or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function deleteList($data)
    {
        return new ApiProblem(405, 'The DELETE method has not been defined for collections');
    }

    /**
     * Fetch a resource
     *
     * @param  mixed $id
     * @return ApiProblem|mixed
     */
    public function fetch($id)
    {
        return $this->mapper->fetchOne($id);
        //return new ApiProblem(405, 'The GET method has not been defined for individual resources');
    }

    /**
     * Fetch all or a subset of resources
     *
     * @param  array $params
     * @return ApiProblem|mixed
     */
    public function fetchAll($params = array())
    {
       	$user = $this->getIdentity()->getAuthenticationIdentity();

        return $this->mapper->fetchAll($params, $user);
        //return new ApiProblem(405, 'The GET method has not been defined for collections');
    }

    /**
     * Patch (partial in-place update) a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function patch($id, $data)
    {
        return new ApiProblem(405, 'The PATCH method has not been defined for individual resources');
    }

    /**
     * Replace a collection or members of a collection
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function replaceList($data)
    {
        return new ApiProblem(405, 'The PUT method has not been defined for collections');
    }

    /**
     * Update a resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function update($id, $data)
    {
        $user = $this->getIdentity()->getAuthenticationIdentity();

        //print_r($user);

        if(! $user) return ApiProblem(401, 'Unauthorized');

        $email = $user['user_id'];

        return $this->mapper->update($data, $email);

        //return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
