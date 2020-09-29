<template>
    <div>
        <input type="file" @change="select">
        <progress :value="progress"></progress>
    </div>
</template>

<script>
    export default {
        watch: {
            chunks(n, o) {
                console.log(o)
                if (n.length > 0) {
                    this.upload();
                }
            }
        },

        data() {
            return {
                file: null,
                chunks: [],
                uploaded: 0
            };
        },

        computed: {
            progress() {
                if (this.file != null) {
                    return Math.floor((this.uploaded * 100) / this.file.size);
                }
                return 0
            },
            formData() {
                let formData = new FormData;

                formData.set('is_last', this.chunks.length === 1);
                formData.set('file', this.chunks[0], `${this.file.name}.part`);

                return formData;
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
            select(event) {
                this.file = event.target.files.item(0);
                this.createChunks();
            },
            upload() {
                window.axios(this.config).then(response => {
                    console.log(response)
                    this.chunks.shift();
                }).catch(error => {
                    console.log(error)
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