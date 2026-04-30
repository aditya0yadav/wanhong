<template>
    <video ref="myPlay" class="video-js"></video>
  </template>
  <script>
  import { ref, reactive, toRefs, onMounted, onBeforeMount } from "vue";
  import "video.js/dist/video-js.css";
  import videojs from "video.js";
  
  export default {
    setup () {
      let player = null; // 妈的vue有bug 这个写成响应式数据 切换video地址会加载不出来
      let myPlay = ref(null);
      let init = reactive({
        options: {
          autoplay: 'auto',
          controls: true,
          fill: true, // 填充模式
        },
      })
      onMounted(() => {
        player = videojs(myPlay.value, init.options)
  
        player.on("loadstart", function () {
          console.log("开始请求数据 ");
        });
        player.on("progress", function () {
          console.log("正在请求数据 ");
        });
        player.on("loadedmetadata", function () {
          console.log("获取资源长度完成 ");
        });
        player.on("canplaythrough", function () {
          console.log("视频源数据加载完成");
        });
        player.on("waiting", function () {
          console.log("等待数据");
        });
        player.on("play", function () {
          console.log("视频开始播放");
        });
        player.on("playing", function () {
          console.log("视频播放中");
        });
        player.on("pause", function () {
          console.log("视频暂停播放");
        });
        player.on("ended", function () {
          console.log("视频播放结束");
        });
        player.on("error", function () {
          console.log("加载错误");
        });
        player.on("seeking", function () {
          console.log("视频跳转中");
        });
        player.on("seeked", function () {
          console.log("视频跳转结束");
  
        });
        player.on("ratechange", function () {
          console.log("播放速率改变");
        });
        player.on("timeupdate", function () {
          console.log("播放时长改变");
        });
        player.on("volumechange", function () {
          console.log("音量改变");
        });
        player.on("stalled", function () {
          console.log("网速异常");
        });
      })
      function initVideo(opt) {
        player.src(opt)
        player.play()
      }
      onBeforeMount (() => {
        if(player) player.dispose()
      })
  
  
  
      return {
        myPlay,
        ...toRefs(init),
        initVideo
      }
  
      
    }
  
  }
  </script>