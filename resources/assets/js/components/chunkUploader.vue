<template>
    <div>
        <input type="file" @change="select">
        <progress :value="progress"></progress>
    </div>
</template>

<script>
    import FileStreamer from '../fileStreamer.js'
    export default {
        // watch: {
        //     chunks(n, o) {
        //         if (n.length > 0) {
        //             this.upload();
        //         }
        //     }
        // },

        data() {
            return {
                file: null,
                chunks: [],
                uploaded: 0,
                formData: []
            };
        },

        computed: {
            progress() {
                if (this.file != null) {
                    return Math.floor((this.uploaded * 100) / this.file.size);
                }
                return 0
            },

            config() {
                return {
                    method: 'POST',
                    data: this.formData,
                    url: 'api/uploadChunk',
                    headers: {
                        'Content-Type': 'application/octet-stream'
                    },
                    onUploadProgress: event => {
                        this.uploaded += event.loaded;
                    }
                };
            }
        },

        methods: {
            createFormData(num) {
                this.formData = new FormData()

                this.formData.set('is_last', this.chunks.length === 1);
                this.formData.set('current_num', num);
                this.formData.set('file', this.chunks[num], `${this.file.name}.part`);
                // formData.set('file', this.chunks[0], `${this.file.name}.part`);

                // return formData;
            },
            async select(event) {
                this.file = event.target.files.item(0);
                // const fileStreamer = new FileStreamer(this.file);
                // while (!fileStreamer.isEndOfFile()) {
                //   const data = await fileStreamer.readBlockAsText();

                //   this.uploadFile(data)
                // }

                this.createChunks();

                for (var i = 0; i < this.chunks.length; i++) {
                    this.upload(i)
                }
            },

            uploadFile(data) {
                this.formData = new FormData()

                // this.formData.set('is_last', this.chunks.length === 1);
                // this.formData.set('current_num', num);
                this.formData.set('file', data, `${this.file.name}.part`);

                window.axios(this.config).then(response => {
                    // console.log(response)
                }).catch(error => {
                    // console.log(error)
                });
            },
            async upload(num) {
                this.createFormData(num)

                await window.axios(this.config).then(response => {
                    // console.log(response)
                    this.chunks.shift();
                }).catch(error => {
                    // console.log(error)
                });
            },
            createChunks() {
                let size = 100000, chunks = Math.ceil(this.file.size / size) / 10;
                // let size = 2048, chunks = Math.ceil(this.file.size / size);

                for (let i = 0; i < chunks; i++) {
                    this.chunks.push(this.file.slice(
                        i * size, Math.min(i * size + size, this.file.size), this.file.type
                    ));
                }
            }
        }
    }
</script>


