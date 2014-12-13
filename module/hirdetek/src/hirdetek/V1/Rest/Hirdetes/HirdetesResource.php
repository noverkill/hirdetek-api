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

        $request = $this->getEvent()->getRequest();

        $files = $request->getfiles();

        $filename = '';

        if($files->count() > 0) {

            foreach ($files as $files) {
                
                //print_r($files);
                
                $filename = str_replace(' ', '.', microtime()) . "_" . $files['name'];

                $upload_dir = "./upload/"; 
                
                $time = time();

                $folder_name = date('Y', $time) . '/' . date('m', $time) . '/' . date('d', $time) . '/'; 

                //print $upload_dir . $folder_name;

                if(! is_dir($upload_dir . $folder_name)) mkdir($upload_dir . $folder_name, 0755, true);

                move_uploaded_file($files['tmp_name'], $upload_dir . $folder_name . $filename);
            }

            return;
        }

        $user = $this->getIdentity()->getAuthenticationIdentity();

        return $this->mapper->create($data, $user);

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
        return $this->mapper->delete($id);
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
        return $this->mapper->fetchAll($params);
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
        return $this->mapper->update($data);
        //return new ApiProblem(405, 'The PUT method has not been defined for individual resources');
    }
}
