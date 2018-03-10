# Laravel CRUD Trait for use with Element-UI-CRUD

Note that this should be considered unstable and not secure.

Pull requests to improve on this would be much welcomed.

## Installation

You can copy this where you like, but my preference is usually to create a app/Traits folder within my projects and place this file there.

## Usage

Most options here are not documented as they are not finalized. However, a sample controller is given below demonstrating some functionality.

```PHP
<?php

namespace App\Http\Controllers\API;

use App\Document;
use App\Traits\CRUD;

/**
* Controller offering CRUD functionality for Documents
*/
class DocumentsController extends Controller {

    use CRUD;
    
    // This function is used to configure CRUD options
    public function crud($action, $params)
    {
        $this->set('model', \App\Document::class)
             ->set(['list', 'show'], ['id', 'title', 'content'])
             ->set(['create', 'store', 'edit', 'update'], ['title', 'content']);
        
        // Hook for modifying the query
        // Useful if you want to add a WHERE statement
        /*
        $this->set('hooks.query'), function($query) {
            $query->whereNull('project_id');
        });
        */
        
        // Hook for modifying before storage
        // Useful if you need to set a variable based on an added query parameter (stored in $params)
        /*
        $this->set(['hooks.store', 'hooks.update'], function($entity) use($params) {
            $entity->project_id = $params['project_id'];
            return $entity;
        });
        */
        
        // Because the hooks are stored as an array, you can "append" to them using $this->push
        /*
        if ($params['project_id') {
            $this->push(['list', 'show'], ['project_id'])
                ->push('hooks.query', function($query) use($params) {
                    $query->where('project_id', $params['project_id']);
                })->push(['hooks.store', 'hooks.update'], function($entity) use($params) {
                    $entity->project_id = $params['project_id'];
                    return $entity;
                });
        }
        */
    }
    

}

```
