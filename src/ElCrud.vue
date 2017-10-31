<template>
    <div>
        <div class="list-buttons" style="margin-bottom:1em;">
          <el-button-group>
            <template>
              <slot name="buttons"></slot>
            </template>
            <el-button v-if="create.length" type="primary" icon="el-icon-document" v-on:click="setAction('create')">Create</el-button>
            <el-button v-if="showRefresh" type="primary" icon="el-icon-refresh" v-on:click="$refs.list.fetchData();"></el-button>
          </el-button-group>
        </div>
        
        <List ref="list"
              :primary-key="primaryKey"
              :endpoint="endpoint"
              :columns="list"
              :titles="titles"
              :params="params"
              :order="order"
              :pagination="pagination"
              :selectable="selectable"
              :row-class-name="rowClassName">
          <template v-for="column in list" :slot="column" slot-scope="scope">
            <slot :name="'list.'+column" :row="scope.row">{{ fromDotNotation(scope.row, column) }}</slot>
          </template>
          <template slot-scope="scope">
            <slot name="list" :row="scope.row"></slot>
            <el-button v-if="edit.length" size="small" type="primary" v-on:click="setAction('edit', scope.row[primaryKey])" icon="el-icon-edit"></el-button>
            <el-button v-if="showDelete" size="small" type="danger" v-on:click="setAction('delete', scope.row[primaryKey])" icon="el-icon-delete"></el-button>
          </template>
        </List>
        
        <!--
        <Show v-else-if="currentAction === 'show'" :endpoint="endpoint">
          <template scope="props">
            <a class="btn btn-default btn-sm" v-on:click="setCurrentAction('edit', props.row['id'])"><i class="glyphicon glyphicon-search" title="View"></i></a>
            <a class="btn btn-default btn-sm" v-on:click="setCurrentAction('edit', props.row['id'])"><i class="glyphicon glyphicon-edit" title="Edit"></i></a>
            <a class="btn btn-default btn-sm" v-on:click="setCurrentAction('edit', props.row['id'])"><i class="glyphicon glyphicon-remove" title="Remove"></i></a>
          </template>
        </Show>
        -->
        
        <el-dialog v-if="action === 'create'" title="Create" :visible="action === 'create'" :before-close="handleClose"
                   :width="createSize" :modal-append-to-body="modalAppendToBody">
          <Create :endpoint="endpoint"
                  :columns="create"
                  :titles="titles"
                  :fields="fields"
                  :rules="rules"
                  :params="params"
                  :after="closeAndRefreshList">
            <template v-for="column in create" :slot="column" slot-scope="scope">
              <slot :name="'create.'+column" :entity="scope.entity">
                <el-crud-field :name="column" :fields="fields" :titles="titles" v-model="scope.entity[column]"></el-crud-field>
              </slot>
            </template>
          </Create>
        </el-dialog>
        
        <el-dialog v-if="action === 'edit'" title="Edit" :visible="action === 'edit'" :before-close="handleClose"
                   :width="editSize"  :modal-append-to-body="modalAppendToBody">
          <Edit ref="edit"
                :endpoint="endpoint+'/'+entityId"
                :columns="edit"
                :titles="titles"
                :fields="fields"
                :rules="rules"
                :params="params"
                :after="closeAndRefreshList">
            <template v-for="column in edit" :slot="column" slot-scope="scope">
              <slot :name="'edit.'+column" :entity="scope.entity">
                <el-crud-field :name="column" :fields="fields" :titles="titles" v-model="scope.entity[column]"></el-crud-field>
              </slot>
            </template>
          </Edit>
        </el-dialog>
        
        <el-dialog v-if="action === 'delete'" title="Confirm" :visible="action === 'delete'" :before-close="handleClose"
                  :width="deleteSize" :modal-append-to-body="modalAppendToBody">
          <Delete :endpoint="endpoint+'/'+entityId"
                  :after="closeAndRefreshList">
          </Delete>
        </el-dialog>
        
    </div>
</template>

<script>
    import List from './List.vue'
    import Show from './Show.vue'
    import Create from './Create.vue'
    import Edit from './Edit.vue'
    import Delete from './Delete.vue'
    import ElCrudField from './Field.vue'
    import Helpers from './Helpers.vue'
    
    export default {
        components: { List, Show, Create, Edit, Delete, ElCrudField },
        mixins: [ Helpers ],
      
        props: {
          endpoint: String,
          primaryKey: { type: String, default: 'id' },
          list: { type: Array, default: () => { return []; } },
          show: { type: Array, default: () => { return []; } },
          create: { type: Array, default: () => { return []; } },
          edit: { type: Array, default: () => { return []; } },
          showDelete: { type: Boolean, default: true },
          showRefresh: { type: Boolean, default: true },
          titles: { type: Object, default: () => { return {}; } },
          rules: { type: Object, default: () => { return {}; } },
          order: { type: Object, default: () => { return {}; } },
          params: { type: Object, default: () => { return {}; } },
          fields: { type: Object, default: () => { return {}; } },
          after: { type: Function, default: null },
          pagination: { type: Number, default: 0 },
          selectable: { type: Boolean, default: false },
          rowClassName: { type:Function, default: null },
          createSize: { type: String, default: '75%' },
          editSize: { type: String, default: '75%' },
          deleteSize: { type: String, default: '50%' },
          modalAppendToBody: { type: Boolean, default: true },
        },
        
        data () {
          return {
            action: '',
            entityId: null,
          }
        },
        
        methods: {
          
          setAction: function(action, id, after) {
              this.action = action;
              this.entityId = id;
          },
          
          closeAndRefreshList: function() {
            this.setAction('', null);
            this.$refs.list.fetchData();
            
            // Perform callback upon action
            if (this.after) {
              this.after();
            }
          },
          
          handleClose: function(done) {
            this.setAction('', null);
          },
        },
    }
</script>
