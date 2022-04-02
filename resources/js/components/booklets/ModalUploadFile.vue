<template>
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sección: {{ row.Seccion && row.Seccion.toString().padStart(4, '0') }} - {{ row.NombreCasilla}}</h5>
                </div>
                <div class="modal-body">
                    <dropzone id="dropzone" :url="url" :params="row"></dropzone>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="closeModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DropZone from "./DropzoneComponent.vue"

export default {
    props: ['row'],
    components: {
        DropZone
    },
    data(){
        return {
            url: '/admin/cuadernillos/upload/file'
        }
    },
}
</script>
