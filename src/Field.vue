<template>
    <div>
        <el-form-item v-if="fields.hasOwnProperty(name) && fields[name].type == 'textarea'" :label="(titles.hasOwnProperty(name)) ? titles[name] : name" :prop="name">
          <el-input v-model="value" v-on:input="$emit('input', value)" type="textarea" :autosize="{ minRows: 6 }"></el-input>
        </el-form-item>
        
        <el-form-item v-else-if="fields.hasOwnProperty(name) && fields[name].type == 'checkbox'" :prop="name">
          <el-checkbox v-model="value" v-on:input="$emit('input', value)">{{ (titles.hasOwnProperty(name)) ? titles[name] : name }}</el-checkbox>
        </el-form-item>
        
        <el-form-item v-else-if="fields.hasOwnProperty(name) && fields[name].type == 'switch'" :label="(titles.hasOwnProperty(name)) ? titles[name] : name" :prop="name">
          <el-switch active-text="" inactive-text="" v-model="value" v-on:input="$emit('input', value)"></el-switch>
        </el-form-item>
        
        <el-form-item v-else-if="fields.hasOwnProperty(name) && fields[name].type == 'select'" :label="(titles.hasOwnProperty(name)) ? titles[name] : name" :prop="name"><br/>
          <el-select v-model="value" v-on:input="$emit('input', value)">
            <el-option v-for="(option, index) in fields[name].options" :key="index" :label="option.label" :value="option.value"></el-option>
          </el-select>
        </el-form-item>
        
        <el-form-item v-else :label="(titles[name]) ? titles[name] : name" :prop="name">
          <el-input v-model="value" v-on:input="$emit('input', value)"></el-input>
        </el-form-item>
    </div>
</template>

<script>
    export default {
      
        props: ['name', 'fields', 'titles', 'value'],
        
        data () {
          return {
            data: [],
            error: null,
          }
        },
        
        created() {
        },
              
        methods: {

        }
    }
</script>
