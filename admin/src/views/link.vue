<template>
    <div class="link-content">{{ pid }}</div>
</template>
<script>
import request from '@/utils/request'
export default {
    name: 'link',
    data(){
        return {
            pid: '',
            key: ''
        }
    },
    created(){
        this.pid = this.$route.query.pid;
        this.key = this.$route.query.key;
        const data = request({
                url: '/api/index/topic',
                method: 'get',
                params: {
                    key: this.key,
                    pid: this.pid
                }
            })
    }
}
</script>
<style lang="scss" scoped>
  
</style>