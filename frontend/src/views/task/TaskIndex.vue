<template>
  <div class="q-pa-md">
    <div class="row justify-end q-pb-sm">
      <q-btn push type="submit" label="Add Task" class="q-mt-md" color="primary" @click="openAddTaskDialog" />
    </div>

    <q-table
        flat bordered
        ref="tableRef"
        title="Tasks"
        :rows="rows"
        :columns="columns"
        row-key="name"
        binary-state-sort
        v-model:pagination="pagination"
        :loading="loading"
        @request="fetchTasks"
    >
      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="title" :props="props">
            {{ props.row.title }}
          </q-td>
          <q-td key="description" :props="props">
            {{ props.row.description }}
            <q-popup-edit v-model="props.row.description" title="Update description" buttons v-slot="scope" @update:modelValue="updateTask('description', props.row)">
              <q-input outlined dense autofocus lazy-rules type="textarea" v-model="scope.value"
                :rules="[ val => val !== null && val !== '' || 'Status is required!']" />
            </q-popup-edit>
            <q-tooltip anchor="top middle" self="top middle" :offset="[10, 10]">
              <strong>Click here to edit</strong>
            </q-tooltip>
          </q-td>
          <q-td key="status" :props="props">
            <span :class="props.row.status === 'Completed' ? 'text-positive' : 'text-primary'">
              {{ props.row.status }}
            </span>
            <q-popup-edit v-model="props.row.status" title="Update status" buttons v-slot="scope" @update:modelValue="updateTask('status', props.row)">
              <q-select outlined dense autofocus lazy-rules v-model="scope.value" :options="['Completed', 'Pending']"
                :rules="[ val => val !== null && val !== '' || 'Status is required!']" />
            </q-popup-edit>
            <q-tooltip anchor="top middle" self="top middle" :offset="[10, 10]">
              <strong>Click here to edit</strong>
            </q-tooltip>
          </q-td>
          <q-td key="action" :props="props">
            <q-btn flat round color="negative" size="small" icon="delete" @click="deleteTask(props.row.id)">
              <q-tooltip anchor="bottom middle" self="top middle" :offset="[10, 10]">
                <strong>Delete</strong>
              </q-tooltip>
            </q-btn>
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useTaskStore } from '@/stores/task'
import AddTaskDialog from '@/views/task/AddTaskDialog.vue'

const columns = [
  { name: 'title', align: 'left', label: 'Title', field: 'title', sortable: true },
  { name: 'description', align: 'left', label: 'Description', field: 'description', sortable: true },
  { name: 'status', align: 'left', label: 'Status', field: 'status', sortable: true, style: 'width: 10px' },
  { name: 'action', label: 'Action', field: 'action' }
]

export default {
  setup () {
    const $q = useQuasar()
    const rows = ref([])
    const loading = ref(false)
    const pagination = ref({
      sortBy: 'desc',
      descending: false,
      page: 1,
      rowsPerPage: 10,
      rowsNumber: 0
    })

    function fetchTasks (props = {}) {
      loading.value = true
      pagination.value.page = props?.pagination?.page || 1
      pagination.value.rowsPerPage = props?.pagination?.rowsPerPage || 10

      const { page, rowsPerPage } = pagination.value

      useTaskStore().fetchTasks({ page, per_page: rowsPerPage })
        .then(response => {
          pagination.value.rowsNumber = response.total
          rows.value = response.data
        })
        .catch(error => {
          $q.notify({
            type: 'negative',
            position: 'top-right',
            message: error?.response?.data?.message || 'Something went wrong! Please try again later'
          })
        })
        .finally(() => {
          loading.value = false
        })
    }

    function deleteTask (id) {
      $q.dialog({
        title: 'Confirm',
        message: 'Are you sure you want to delete this task?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        useTaskStore().deleteTask(id)
          .then(response => {
            fetchTasks({
              pagination: {
                page: pagination.value.page,
                rowsPerPage: pagination.value.rowsPerPage
              }
            })
            setTimeout(() => {
              $q.notify({
                type: 'positive',
                position: 'top-right',
                message: response.message
              })
            }, 1000)
          })
          .catch(error => {
            $q.notify({
              type: 'negative',
              position: 'top-right',
              message: error?.response?.data?.message || 'Something went wrong! Please try again later'
            })
          })
      })
    }

    function updateTask (key, param) {
      useTaskStore().updateTask(param.id, { [key]: param[key] })
        .then(() => {
          $q.notify({
            type: 'positive',
            position: 'top-right',
            message: `${key.charAt(0).toUpperCase() + key.slice(1)} updated successfully!`
          })
        })
        .catch(error => {
          $q.notify({
            type: 'negative',
            position: 'top-right',
            message: error?.response?.data?.message || 'Something went wrong! Please try again later'
          })
        })
    }

    function openAddTaskDialog () {
      $q.dialog({
        component: AddTaskDialog
      }).onOk(response => {
        rows.value.pop()
        rows.value.unshift(response)
        pagination.value.rowsNumber++

        $q.notify({
          type: 'positive',
          position: 'top-right',
          message: 'New task added successfully!'
        })
      })
    }

    onMounted(() => {
      fetchTasks()
    })

    return {
      columns,
      rows,
      loading,
      pagination,
      fetchTasks,
      updateTask,
      deleteTask,
      openAddTaskDialog
    }
  }
}
</script>
