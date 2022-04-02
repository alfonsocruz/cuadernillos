<template>
  <div>
    <data-table
      :columns="columns"
      :data="data"
      @on-table-props-changed="reloadTable"
      @loading="isLoading = true"
      @finishedLoading="isLoading = false"
      :perPage="perPage"
      :translate="translate"
    >
    <div slot="filters" slot-scope="{ tableFilters, perPage }">
        <div class="row mb-2">
            <div class="col-md-1">
                <label>Mostar </label>
                <select class="form-control" v-model="tableFilters.length">
                    <option :key="page" v-for="page in perPage">{{ page }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Región </label>
                <select
                    v-model="tableFilters.filters.idRegion"
                    class="form-control">
                    <option v-for="region in catRegions" :value="region.value">{{region.text}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Municipio </label>
                <select
                    v-model="tableFilters.filters.idMunicipioReportes"
                    class="form-control">
                    <option v-for="mun in catMunicipalities" :value="mun.value">{{mun.text}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Sección</label>
                <select
                    v-model="tableFilters.filters.Seccion"
                    class="form-control">
                    <option v-for="seccion in catSections" :value="seccion.value">{{seccion.text}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Usuario</label>
                <select
                    v-model="tableFilters.filters.UserUpdate"
                    class="form-control">
                    <option v-for="user in catUsers" :value="user.value">{{user.text}}</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Tiene cuadernillo </label>
                <select
                    v-model="tableFilters.filters.TieneCuadernillo"
                    class="form-control">
                    <option value='-1'>Todos</option>
                    <option value='1'>Con Cuadernillo</option>
                    <option value='0'>Sin Cuadernillo</option>
                </select>
            </div>
        </div>
    </div>
    </data-table>
    <!-- <loading :is-full-page="true" :active.sync="isLoading"> </loading> -->
    <modal
        :row="selectedRow">
    </modal>
  </div>
</template>

<script>
import DataTable from "laravel-vue-datatable";
import Modal from './ModalUploadFile.vue';
// import Loading from "vue-loading-overlay";
// import "vue-loading-overlay/dist/vue-loading.css";
import UploadButton from "./UploadButton.vue";
import swal from "sweetalert2";

Vue.use(DataTable);
export default {
  props: ["url"],
  components: {
    Modal,
    UploadButton
  },
  data() {
    return {
      isLoading: false,
      data: {},
      tableProps: {
        search: "",
        length: 20,
        column: "id",
        dir: "asc",
      },
      perPage: ["10", "20", "50", "100"],
      columns: [
        {
          label: "DF",
          name: "idDF",
          orderable: true,
        },
        {
          label: "DL",
          name: "idDL",
          orderable: true,
        },
        {
          label: "Region",
          name: "Region",
          orderable: true,
        },
        {
          label: "Municipio",
          name: "Municipio",
          orderable: true,
        },
        {
          label: "Sección",
          name: "Seccion",
          orderable: true,
          searchable: true,
        },
        {
          label: "Casilla",
          name: "NombreCasilla",
          orderable: true,
          searchable: true,
        },
        {
          label: "Tiene cuadernillo",
          name: "TieneCuadernillo",
          orderable: true,
          classes: {
            "text-center": true,
          },
        },
        {
          label: "Cargar Archivo",
          name: "Subir",
          orderable: false,
          classes: {
            btn: true,
            "btn-primary": true,
            "btn-sm": true,
          },
          event: "click",
          handler: this.updateSelectedModal,
          component: UploadButton,
        },
        {
          label: "Archivo",
          name: "NombreArchivo",
          orderable: true,
        },
        {
          label: "Usuario",
          name: "name",
          orderable: true,
        },
      ],
      filters: {
        TieneCuadernillo: '',
        idRegion: '',
        idMunicipioReportes: '',
        Seccion: ''
      },
      selectedRow: {},
      translate: {
        nextButton: 'Siguiente', previousButton: 'Anterior', placeholderSearch: 'Buscar', info: 'Mostrando'
      },
      catRegions: [],
      catMunicipalities: [],
      catSections: [],
      catUsers: []
    };
  },
  created() {
    this.getData(this.url);
    this.getCatalogs();
  },
  methods: {
    getData(url = this.url, options = this.tableProps) {
      axios
        .get(url, {
          params: options,
        })
        .then((response) => {
          let records = [];
          if (response.data.data !== undefined) {
            records = response.data.data.map((item) => {
              item.Seccion = item.Seccion.toString().padStart(4, '0');
              item.TieneCuadernillo = item.TieneCuadernillo === 1 ? "SI" : "NO";
              return item;
            });
          }
          this.data = {
            ...response.data,
            data: records,
          };
        })
        // eslint-disable-next-line
        .catch((errors) => {
          //Handle Errors
        });
    },
    reloadTable(tableProps) {
      this.getData(this.url, tableProps);
    },
    updateSelectedModal(data) {
      this.selectedRow = data;
    },
    getCatalogs(){
        axios
        .post('/admin/catalogs/get', {
          catalogs: ['regiones', 'municipios_reportes', 'secciones', 'usuarios'],
        })
        .then((res) => {
          return res.data;
        })
        .then((res) => {
          if(res.success){
            this.catRegions = res.data['regiones'] ? res.data['regiones'] : [];
            this.catMunicipalities = res.data['municipios_reportes'] ? res.data['municipios_reportes']: [];
            this.catSections = res.data['secciones'] ? res.data['secciones']: [];
            this.catUsers = res.data['usuarios'] ? res.data['usuarios']: [];
          }else{
            swal.fire({ title: response.error, icon: "warning" });
          }
        })
        // eslint-disable-next-line
        .catch((errors) => {
          //Handle Errors
        });
    }
  },
};
</script>
