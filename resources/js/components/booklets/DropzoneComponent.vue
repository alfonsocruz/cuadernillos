<style lang="scss">
@import "~vue2-dropzone/dist/vue2Dropzone.min.css";
</style>

<template>
  <div id="drop-area">
    <vue-dropzone
      ref="dropzone"
      id="drop1"
      :options="dropOptions"
      @vdropzone-complete="afterComplete"
      @vdropzone-mounted="onMounted"
    ></vue-dropzone>
    <!-- <input type="hidden" id="filename" :value="filename" /> -->
    <button @click="removeAllFiles" id="btnDelete" class="btn btn-danger">
      Eliminar
    </button>
    <b-button variant="primary" class="pull-right" @click="store()"
      >Guardar</b-button
    >
  </div>
</template>

<script>
import vueDropzone from "vue2-dropzone";
import swal from "sweetalert2";

export default {
  components: {
    vueDropzone,
  },
  props: ["url", "params"],
  data: () => ({
    dropOptions: {
      url: "https://fake.com/post",
      maxFilesize: 30, // MB
      maxFiles: 1,
      // chunking: true,
      // chunkSize: 500, // Bytes
      // thumbnailWidth: 150, // px
      // thumbnailHeight: 150,
      addRemoveLinks: true,
      acceptedFiles: "image/*,application/pdf",
      headers: {
        "X-CSRF-TOKEN": document.head.querySelector('[name="csrf-token"]')
          .content,
      },
    },
    disabled: true,
    filename: "",
  }),
  methods: {
    removeAllFiles() {
      this.$refs.dropzone.removeAllFiles();
      this.filename = "";
      this.disabled = true;
    },
    afterComplete(file) {
      if (file.status == "success") {
        let response = JSON.parse(file.xhr.response);
        this.filename = response.data;
        this.disabled = false;
      }
    },
    onMounted() {
      this.$refs.dropzone.setOption("url", this.url);
    },
    store() {
      axios
        .post("/admin/cuadernillos/store/file", {
          ...this.params,
          filename: this.filename,
        })
        .then(res => {
          return res.data;
        })
        .then((response) => {
          if (response.success) {
            swal.fire({ title: "Almacenado con éxito", icon: "success" });
            this.removeAllFiles();
            document.getElementById("closeModal").click;
          } else {
            swal.fire({ title: response.error, icon: "warning" });
          }
        })
        // eslint-disable-next-line
        .catch((errors) => {
          console.log(errors)
          swal.fire({ title: 'Error al guardar', icon: "error" });
        });
    },
  },
};
</script>
