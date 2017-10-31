# Element-UI CRUD

CRUD components for Element-UI (Vue).

This is intended for use with Laravel's Resource Controllers:

https://laravel.com/docs/5.5/controllers#resource-controllers

However, it may be used in other HTTP applications if the same endpoints are implemented.


## Installation

Requires:

```
npm install axios --save
npm install vue --save
npm install element-ui --save
```

Install:

```
npm install element-ui-crud --save
```

Configuration:

You must set Axios as your $http library:

```
import Axios from 'axios'
Vue.prototype.$http = Axios;
```


## Components

Element-UI CRUD is designed to be modular so that each component can be used on its own if desired.

### ElCrud

The primary component that provides full CRUD functionality.

#### Example

```vue
<template>
    <div>
        <h1>Users</h1>
        <hr/>
        
        <el-crud endpoint="/api/users"
                  :list="['id', 'name']"
                  :create="['name']"
                  :edit="['name']"
                  :titles="{ 'id': 'ID', 'name': 'Name' }"
          <template slot="list" scope="scope">
              <el-button size="small" type="primary" v-on:click="alert(scope.row)" icon="information"></el-button>
          </template>
        </el-crud>
    </div>
</template>

<script>
    import ElCrud from "element-ui-crud"

    export default {
        components: { ElCrud },
    }
</script>
```

#### Over-riding/Customizing Display of Columns

The display of columns can be over-ridden using Vue's Template/Slot system. The general format is "component.columnName". An example is below showing how to over-ride the "id" column in the "List" component.

```vue
<el-crud endpoint="/api/users"
          :list="['id', 'name']"
          :create="['name']"
          :edit="['name']"
          :titles="{ 'id': 'ID', 'name': 'Name' }"
  <template slot="list" scope="scope">
      <el-button size="small" type="primary" v-on:click="console.log(scope.row)" icon="information"></el-button>
  </template>
  <template slot="list.id" scope="scope">
      ID of user is {{ scope.row['id'] }} 
  </template>
</el-crud>
```

If you plan on using a custom component on the Create or Edit component, you must bind the v-model as below:

```vue
<template slot="edit.data" scope="scope">
    <code-editor v-model="scope.entity.data"></code-editor>
</template>
```

(Note that the component MUST support the use of v-model for this to work.)

#### Supported Parameters

- endpoint

  The root HTTP REST endpoint of your backend that provides CRUD functionality.
  Note that this should implement the same endpoints as Laravel's Resource Controller.
  
  - **Type:** string
  - **Example:** "/api/users"

- primary-key (optional)

  The primary key to use on the List component.
  
	- **Type:** string
    - **Default:** "id"

- list

    Fields that should be shown on the List component.
    
    - **Type:** array
    - **Example:** ["id", "name"]

- create

    Fields that should be shown on the Create component.
    If not specified, create functionality will not be offered.
    
    - **Type:** array
    - **Example:** ["name"]

- edit

    Fields that should be shown on the Edit component.
    If not specified, edit functionality will not be offered.
    
    - **Type:** array
    - **Example:** ["name"]

- show-delete

	Whether "Delete" should be shown next to items in row.
    
    - **Type:** boolean
    - **Default:** true

- show-refresh

	Whether "Refresh" button should be shown above table.
    
    - **Type:** boolean
    - **Default:** true

- titles

    Titles to apply to List Columns and Create/Edit Forms.
    If not provided, the key for that column is used.

    - **Type:** Object
    - **Example:** { id: 'ID', name: 'Name' }

- order (TODO)

    Titles to apply to List Columns and Create/Edit Forms.
    If not provided, the key for that column is used.

    - **Type:** Object
    - **Example:** { id: 'ID', name: 'Name' }

- params

    Extra parameters that should be passed with every request.
    These will be passed in the query string.

    - **Type:** Object
    - **Example:** { where: 'user.type == "ADMIN' }

- fields (TODO)

    Extra parameters that should be passed with every request.
    Can be used for filtering the results server-side.

    - **Type:** Object
    - **Example:** { level: 'admin' }

- after

    Function to perform after Create/Edit.
    For example, after a edit is performed, you might wish to update a VueX store.
    
    - **Type:** function
    - **Example:** function(entity) { this.$store.dispatch('refreshUsers'); }

- pagination

    If set, Pagination will be enabled. This must be handled server-side.
    The "limit" and "offset" parameters will be passed in the Query String.
    
    - **Type:** integer
    - **Default:** 0
    - **Example:** 50

- create-size
	
    Size of the Create Dialog.

    - **Type:** string
    - **Default:** "small"
    - **Example:** "large"

- edit-size

	Size of the Edit Dialog.

    - **Type:** string
    - **Default:** "small"
    - **Example:** "large"

- delete-size

	Size of the Delete Dialog.

    - **Type:** string
    - **Default:** "tiny"
    - **Example:** "small"

### ElList

Component to display the Rows in a table.

See [List.vue](src/List.vue) for a list of available parameters.

### ElEdit

Component to display the Edit Form.

See [Edit.vue](src/Edit.vue) for a list of available parameters.

### ElCreate

Component to display the Create Form.

See [Create.vue](src/Create.vue) for a list of available parameters.

## Badges

![](https://img.shields.io/badge/license-MIT-blue.svg)
![](https://img.shields.io/badge/status-dev-yellow.svg)

---
