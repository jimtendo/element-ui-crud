<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

trait CRUD
{
    static $NO_ERROR = 0;

    public function set($keys, $value)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }
        
        foreach ($keys as $key) {
            $this->crudConfig[$key] = $value;
        }
        
        return $this;
    }
    
    public function push($keys, $value)
    {
        if (!is_array($keys)) {
            $keys = array($keys);
        }
        
        foreach ($keys as $key) {
            $this->crudConfig[$key][] = $value;
        }
        
        return $this;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Initialize CRUD settings
        if ($error = $this->init('list', $request->query())) {
            return response()->json($error, 500);
        }
        
        $query = $this->crudConfig['model']::select($this->crudConfig['list']);
        
        // If "order" is in query string, then apply it
        if ($order = json_decode($request->input('order'))) {
            foreach ($order as $column=>$direction) {
                $query->orderBy($column, $direction);
            }
        }
        
        // If there is a hook to modify the query, run it...
        if (isset($this->crudConfig['hooks.query']) && is_array($this->crudConfig['hooks.query'])) {
            foreach ($this->crudConfig['hooks.query'] as $queryHook) {
                $queryHook($query);
            }
        }
        
        // Get the total number of rows
        $totalQuery = $query;
        $total = $totalQuery->count();
        
        // If there is a limit (e.g. pagination)
        if ($limit = $request->input('limit')) {
            $query->limit($limit);
        }
        
        // If there is an offset (e.g. pagination)
        if ($offset = $request->input('offset')) {
            $query->offset($offset);
        }
        
        $rows = $query->get();
        
        // If there is a hook to modify the query, run it...
        if (isset($this->crudConfig['hooks.list']) && is_array($this->crudConfig['hooks.list'])) {
            foreach ($this->crudConfig['hooks.list'] as $listHook) {
                $listHook($rows);
            }
        }
        
        return ['rows'=>$rows, 'total'=>$total];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        // Initialize CRUD settings
        if ($error = $this->init('create', $request->query())) {
            return response()->json($error, 500);
        } 
        
        $entity = new $this->crudConfig['model'];
        $entity = $this->assignInput($request, 'store', $entity);
    
        return ['entity'=>$entity];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Initialize CRUD settings
        if ($error = $this->init('store', $request->query())) {
            return response()->json($error, 500);
        } 
        
        // Validate input
        if ($error = $this->validateInput($request, 'store')) {
            return response()->json($error, 500);
        }
        
        // Assign input
        $entity = new $this->crudConfig['model'];
        $entity = $this->assignInput($request, 'store', $entity);
        
        // If there is a "store" hook, run it...
        if (isset($this->crudConfig['hooks.store']) && is_array($this->crudConfig['hooks.store'])) {
            foreach ($this->crudConfig['hooks.store'] as $storeHook) {
                $entity = $storeHook($entity);
            }
        }
        
        $entity->save();
        
        return ['entity'=>$entity, 'message'=>'Entry created successfully.'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Initialize CRUD settings
        if ($error = $this->init('show', $request->query())) {
            return response()->json($error, 500);
        } 
    
        // Perform query
        $query = $this->crudConfig['model']::select($this->crudConfig['show'])
                                           ->where('id', $id);
        
        // If there is a hook to modify the query, run it...
        if (isset($this->crudConfig['hooks.query']) && is_array($this->crudConfig['hooks.query'])) {
            foreach ($this->crudConfig['hooks.query'] as $queryHook) {
                $queryHook($query);
            }
        }
        
        $entity = $query->first();
        
        if (!$entity) {
            return response()->json(['message'=>'Entity with ID '.$id.' could not be retrieved.']);
        }
        
        // If there is a hook to modify the query, run it...
        if (isset($this->crudConfig['hooks.show']) && is_array($this->crudConfig['hooks.show'])) {
            foreach ($this->crudConfig['hooks.show'] as $showHook) {
                $showHook($entity);
            }
        }
        
        return ['entity'=>$entity];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        // Initialize CRUD settings
        if ($error = $this->init('edit', $request->query())) {
            return response()->json($error, 500);
        } 
        
        // Check if entity exists
        if ($error = $this->checkEntityExists($id)) {
            return response()->json($error, 500);
        }
        
        $entity = $this->crudConfig['model']::find($id);
        
        // If there is a hook to modify the entity, run it...
        if (isset($this->crudConfig['hooks.edit']) && is_array($this->crudConfig['hooks.edit'])) {
            foreach ($this->crudConfig['hooks.edit'] as $editHook) {
                $editHook($entity);
            }
        }
        
        return ['entity'=>$entity];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Initialize CRUD settings
        if ($error = $this->init('update', $request->query())) {
            return response()->json($error, 500);
        } 
        
        // Check if entity exists
        if ($error = $this->checkEntityExists($id)) {
            return response()->json($error, 500);
        }
        
        $entity = $this->crudConfig['model']::find($id);
        $entity = $this->assignInput($request, 'update', $entity);
        
        // If there is an update hook, run it...
        if (isset($this->crudConfig['hooks.update']) && is_array($this->crudConfig['hooks.update'])) {
            foreach ($this->crudConfig['hooks.update'] as $updateHook) {
                $entity = $updateHook($entity);
            }
        }
        
        $entity->save();
        
        return ['entity'=>$entity, 'message'=>'Entry updated successfully.'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Initialize CRUD settings
        if ($error = $this->init('destroy', $request->query())) {
            return response()->json($error, 500);
        } 
    
        // Check if entity exists
        if ($error = $this->checkEntityExists($id)) {
            return response()->json($error, 500);
        }
        
        $entity = $this->crudConfig['model']::find($id);
        
        // If there is an update hook, run it...
        if (isset($this->crudConfig['hooks.delete']) && is_array($this->crudConfig['hooks.delete'])) {
            foreach ($this->crudConfig['hooks.delete'] as $deleteHook) {
                $entity = $deleteHook($entity);
            }
        }
        
        $entity->delete();
    
        return response()->json(['message'=>'Item successfully deleted.']);
    }
    
    private function init($action, $params)
    {
        $this->crud($action, $params);
        
        // Make sure a model was specified
        if (!isset($this->crudConfig['model'])) {
            return ['status'=>500, 'code'=>20005, 'message'=>'No Model given.', 'developer'=>'You must set a model to use.'];
        }
    }
    
    private function checkEntityExists($id)
    {
        if (!$this->crudConfig['model']::find($id)) {
            return ['status'=>500, 'code'=>20004, 'message'=>'Entity does not exist.', 'developer'=>'Entity '.$id.' does not exist.'];
        }
        
        return self::$NO_ERROR;
    }
    
    private function validateInput($request, $action)
    {
        /*foreach ($this->crudConfig[$action] as $column) {
        
            $properties = $this->crudConfig['columns'][$column];
        
            $value = $request->input($column);
        }*/
        
        return self::$NO_ERROR;
    }
    
    private function assignInput($request, $action, $entity)
    {
        foreach ($this->crudConfig[$action] as $i=>$column) {
            $input = $request->input($column);
            
            if (is_array($input)) {
                $input = json_encode($input);
            }
            
            $entity->{$column} = $input;
        }
        
        return $entity;
    }
}
