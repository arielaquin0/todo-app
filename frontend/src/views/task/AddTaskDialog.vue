<template>
  <q-dialog ref="dialogRef" @hide="onDialogHide">
    <q-card class="q-dialog-plugin" style="width: 780px">
      <q-card-section>
        <div class="q-pl-md q-pr-md">
          <label class="text-h5">Add Task</label>
        </div>
      </q-card-section>
      <q-form @submit="onOkClick" class="q-gutter-md">
        <q-card-section>
          <div class="q-pl-md q-pb-md q-pr-md">
            <div class="q-gutter-y-md column">
              <q-input
                  outlined
                  v-model="form.title"
                  label="Title"
                  lazy-rules
                  :rules="[ val => val !== null && val !== '' || 'Title is required!']"
              />
              <q-input
                  outlined
                  v-model="form.description"
                  label="Description"
                  type="textarea"
                  lazy-rules
                  :rules="[ val => val !== null && val !== '' || 'Description is required!']"
              />
              <q-select
                  outlined
                  v-model="form.status"
                  :options="['Completed', 'Pending']"
                  label="Status"
                  lazy-rules
                  :rules="[ val => val !== null && val !== '' || 'Status is required!']"
              />
            </div>
          </div>
        </q-card-section>
        <q-card-actions align="right">
          <q-btn color="primary" type="submit" label="Save" />
          <q-btn color="primary" label="Cancel" @click="onCancelClick" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<script>
import { ref } from 'vue'
import { useDialogPluginComponent, useQuasar } from 'quasar'
import { useTaskStore } from '@/stores/task'

export default {
  emits: [
    // REQUIRED; need to specify some events that your
    // component will emit through useDialogPluginComponent()
    ...useDialogPluginComponent.emits
  ],

  setup () {
    const $q = useQuasar()
    const { dialogRef, onDialogHide, onDialogOK, onDialogCancel } = useDialogPluginComponent()
    const form = ref({
      title: null,
      description: null,
      status: null
    })

    function show () {
      dialogRef.value.show()
    }

    function onOkClick () {
      useTaskStore().storeTasks(form.value)
        .then(response => {
          onDialogOK(response)
        })
        .catch(error => {
          $q.notify({
            type: 'negative',
            position: 'top-right',
            message: error?.response?.data?.message || 'Something went wrong! Please try again later'
          })
        })
    }

    return {
      form,
      // This is REQUIRED;
      // Need to inject these (from useDialogPluginComponent() call)
      // into the vue scope for the vue html template
      dialogRef,
      onDialogHide,
      onOkClick,
      onCancelClick: onDialogCancel,
      show
    }
  }
}
</script>
