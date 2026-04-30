<template>
    <div style="color:#fff !important;width:100vw;height:100vh;overflow:hidden;position: fixed;">
        <canvas ref="canvas" class="stars-canvas" style="background: #111;"></canvas>
        <div id="container">
            <figure>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0"
                    y="0" viewBox="0 0 136 166" xml:space="preserve">
                    <ellipse id="shad" cx="68" cy="150" rx="40" ry="10"></ellipse>
                    <g clip-path="url(#outer)">
                        <use id="sea" xlink:href="#circ"></use>
                        <g id="boat1" class="vehicles">
                            <use xlink:href="#boat"></use>
                        </g>
                        <g id="land">
                            <use xlink:href="#map" transform="translate(300,-309)"></use>
                            <use xlink:href="#map" transform="translate(600,-309)"></use>
                        </g>
                        <rect id="hi" mask="url(#light)" width="300" height="300"></rect>
                        <g class="vehicles">
                            <g id="plane1">
                                <use xlink:href="#plane"></use>
                            </g>
                            <g id="plane2">
                                <use xlink:href="#plane"></use>
                            </g>
                            <g id="plane3">
                                <use xlink:href="#plane"></use>
                            </g>
                        </g>
                    </g>
                    <defs>
                        <clipPath id="outer">
                            <circle id="circ" cx="68" cy="68" r="68"></circle>
                        </clipPath>
                        <mask id="light">
                            <use xlink:href="#circ" x="-5" y="-12"></use>
                        </mask>
                        <g id="bike">
                            <circle id="head" cx="0.8" cy="4.7" r="0.8"></circle>
                            <path id="ride"
                                d="M4.6,2.2c0,0.4,0.1,0.7,0.2,1L4.3,3.5H3.6c0-0.2-0.2-0.3-0.4-0.3c-0.2,0-0.4,0.2-0.4,0.4v1.1L1.6,5.3 c-0.1,0-0.1,0.10.1,0.1c0,0,0,0,0,0C1.2,5.7,1.2,6.1,1.5,6.3l2.1,1.8c0.2,0.2,0.6,0.2,0.8,0c0,0,0.1-0.1,0.1-0.1l0.4,0.3 C4.7,8.7,4.6,9,4.6,9.4c0,1.2,1,2.2,2.2,2.2S9,10.6,9,9.4c0-1.1-0.9-2.1-2-2.2l0-1.1h0.5C7.8,6.1,8,5.9,8,5.6S7.8,5.1,7.5,5.1H6.7 l-2-1.3l0.4-0.2c0.4,0.5,1,0.8,1.7,0.8C8,4.4,9,3.4,9,2.2S8,0,6.8,0S4.6,1,4.6,2.2z M2.6,5.6l0.8-0.4C3.5,5.2,3.6,5,3.6,4.9V4h0.6 v2.1L3.9,6.8L2.6,5.6z M8.5,9.4c0,1-0.8,1.7-1.7,1.7s-1.7-0.8-1.7-1.7c0-0.3,0.1-0.5,0.2-0.8l1.4,1c0.1,0.1,0.2,0.1,0.2,0 C7,9.6,7,9.5,7,9.4l0,0l0-1.7C7.9,7.8,8.5,8.5,8.5,9.4z M6.6,7.7l0,1.3l-1-0.7C5.8,8,6.2,7.8,6.6,7.7z M6.6,7.2 C6,7.3,5.5,7.6,5.1,8L4.7,7.6v0l0.6-1.5h1.3L6.6,7.2z M5.8,5.1H4.9c-0.1,0-0.2,0-0.2,0.1V4.4L5.8,5.1z M8.5,2.2 c0,1-0.8,1.7-1.7,1.7c-0.5,0-1-0.2-1.3-0.6l1.4-0.9C7,2.4,7,2.3,7,2.2c0,0,0-0.1,0-0.1C6.9,2,6.8,1.9,6.7,2L5.2,3 C5.1,2.7,5.1,2.5,5.1,2.2c0-1,0.8-1.7,1.7-1.7S8.5,1.3,8.5,2.2z">
                            </path>
                        </g>
                        <path id="plane"
                            d="M20,9.7c0,0-0.2-0.9-0.4-1c-0.1,0-1-0.4-2.2-1l0.2-0.9h-1.2l0.1,0.5c-0.7-0.3-1.4-0.6-2-0.9l0.1-0.8h-1.2L13.4,6 c-1.1-0.5-2-0.9-2-0.9C11.4,4.3,11.3,0,10,0S8.6,5.1,8.6,5.1S7.7,5.5,6.6,6l0.1-0.3H5.4l0.1,0.8C4.9,6.7,4.2,7,3.5,7.3l0.1-0.5H2.5 l0.2,0.9c-1.2,0.5-2.1,0.9-2.2,1C0.2,8.8,0,9.7,0,9.7l8.7-1.3l0.4,6.6l-2.7,1.7l0,0.4l3.7-0.5l0,0l3.7,0.5l0-0.4l-2.7-1.7l0.4-6.6 l5.5,0.8L20,9.7z">
                        </path>
                        <path id="boat"
                            d="M4.2,0C2.4,0,0,3.9,0,3.9V4c0,0.2,0.2,0.4,0.4,0.6c0.3,0.2,0.7,0.5,0.7,1.1S0.7,6.6,0.4,6.8C0.2,7.1,0,7.2,0,7.4 v0.1c0,0.2,0.2,0.4,0.4,0.6c0.3,0.2,0.7,0.5,0.7,1.1c0,0.6-0.4,0.9-0.7,1.1C0.2,10.6,0,10.8,0,11v0.1c0,0.2,0.2,0.4,0.4,0.6 c0.3,0.2,0.7,0.5,0.7,1.1S0.7,13.7,0.4,14C0.2,14.2,0,14.3,0,14.5v0.1c0,0.2,0.2,0.4,0.4,0.6c0.3,0.2,0.7,0.5,0.7,1.1 c0,0.6-0.4,0.9-0.7,1.1C0.2,17.7,0.1,17.8,0,18c0.2,1.2,3.3,2,3.3,2l0.1-1.9h3.1l0.1-2h2.2v-2.1H6.8L7,9.1c0,0-1.4-2.5-3.1-2.7 L4.2,0z M4.9,14.9c0.5,0,0.9,0.4,0.9,0.9c0,0.5-0.4,0.9-0.9,0.9c-0.5,0-0.9-0.4-0.9-0.9C4,15.3,4.4,14.9,4.9,14.9z M5.1,12.1 c0.5,0,0.9,0.4,0.9,0.9C6,13.6,5.6,14,5.1,14s-0.9-0.4-0.9-0.9C4.2,12.6,4.6,12.1,5.1,12.1z M5.3,9.4c0.5,0,0.9,0.4,0.9,0.9 c0,0.5-0.4,0.9-0.9,0.9s-0.9-0.4-0.9-0.9C4.3,9.8,4.8,9.4,5.3,9.4z M3.1,1.3c0.3,0,0.5,0.2,0.5,0.5S3.4,2.3,3.1,2.3 c-0.3,0-0.5-0.2-0.5-0.5S2.8,1.3,3.1,1.3z">
                        </path>
                        <path id="map"
                            d="M-205.3,444.2c-1-0.2-1.4-0.7-1.2-1.6c0.6-1.5,4.1,1.4,4.9,1.6C-204,444.3-202.8,444.6-205.3,444.2z M-207.9,442c-0.8-0.4-6.6-5.2-5.7-6.4c1-1.4,0.9-1.2,0.3-3c-1.1-3.4-2.3-5-2.2-8.9c0.1-4.6,0.6-14.7-3.4-17.9 c-2.2-1.7-3.9-2.7-5.4-5.3c-0.8-1.5-3.6-5.2-3.9-6.7c-0.8-4.6,4.2-8,3.1-13.1c-0.5-2.3-6,1.2-7.3-3.5c-1-3.5-6.8-4.7-9.5-4.9 c-1.7-0.1-3.7-1.4-5-2.3c-2.9-1.9-1.2-3.1-2.7-5.6c-0.9-1.4-3.2-7.7-4.4-8.2c-2-0.9,0.8,8.4,1.3,8.4c-2.1-0.1-4.4-10.4-5.3-11.7 c-2.3-3.6,0.3-7.4,2.4-10.2c1.4-1.9,3.2-2.3,3.8-3.6c0-0.1-1.5-4.1-1.7-4.5c-0.7-1.3,2.3-3.6-0.6-5.3c-8.7-5-13,2.8-21.2,2.7 c0.8,0,4.6-2,5-2.7c-0.1,0.1-2.4-0.5-2.7-0.6c0.5-0.2,7.2-6.2,7.6-5.1c0-0.1-3.6,0.4-3.3-0.3c1.6-1.5,3.8-0.4,5.6-1.5 c-2.3-2.2,6.2-3.1,7.6-3.2c4.8-0.4,10.2,2.3,14.7,1.1c1.2-0.3,11.8-1.4,12.2,0.6c-1.3,1.1,7.3,0.7,9,0.8c1.5,0,3.2-0.2,4.7-0.6 c2.2-0.6-0.2-1.3,2.1-2.4c1.5-0.7,2.2,1.6,2.8,2.6c0.7,1.2,2.6-1.5,4-1c1.8,0.7-7.2,4.9-7.4,3.1c0.3,0.4,0.5,0.7,0.8,1 c-2.3,0.7-8.8,2.3-9.6,5.1c-0.5,1.5,4.9,3.5,5.7,3.6c1.9,0.4-0.9,4,1.1,3.9c3-0.1,5.7-7.3,7.3-9.2c1.2-1.4,4.9-2.1,5.6,0.3 c1.4,4.4,0.7,0.6,3.8,0.4c0.8,0,0.5,5.4,0.5,5.8c0,0.4,5.7,0.7-0.4,3c-2.5,0.9-5.1,0.1-7.5,1.2c0.5-0.2-3.3,1.6-2.3,1.8 c0.3,0,4.7-1.5,4.3-0.7c-0.2,0.4,0.5,6.7-2.6,4.7c-0.8-0.5,2-1.1,1.9-1.6c-0.8-2.2-12.8,7.5-13.2,7.1c1.5,0.3-6.8,8-6.7,8 c-0.4,1.3,0.2,2.9,0.1,4.3c-0.2,2.6-1.7-0.1-1.7-1.3c0-5.1-5.7-1.1-7.6-2c-4.4-2-8.6,5-7.4,8.8c0.8,2.4,3.1,3.4,5.3,2 c0,0,4.4-4.8,4.2-1.8c-0.3,4.2-4.5,4.3,1.4,5.1c2.4,0.3,0.4,3.6,0.6,5c0.4,2.7,2.6,1.5,4.3,1.8c2.2,0.5,2,0.4,3.7-1 c0.3-0.2,3.2-2.7,3.5-2.1c-0.1,0.2-1.3,3.4-0.3,3.3c0.8-0.1,0.5-1.3,0.5-1.7c0.2-2.2,4,0.1,4.7,0.4c1.8,0.6,2.9-0.2,4.6,1.3 c2,1.7,2.9,3.1,5.6,3.5c3.6,0.6,3.5,3.9,5.5,6c2.8,2.8,4.7,1.7,8,2.7c4.9,1.6,6,3.9,3.4,8.1c-2.2,3.5-0.6,9.3-3.9,12.1 c-0.3,0.3-2.9,1-3.4,1.2c-2.8,1.3-1.4,1.7-2.3,4.1c-0.9,2.2-1.7,4.6-3.3,6.4c-0.4,0.5-4,0.4-4,0.2c0,1.4,2.6,1.4,1.8,3.2 c-0.9,1-2,1.4-3.3,1.3c-0.4,0.8-0.6,1.6-0.6,2.4c-0.5-0.3-1.1-0.4-1.7-0.5c1.2-1.2,0.1,5.7,0,5.2c0.5,2,1.9,1.4,0.9,4 C-206.7,439.8-206.2,442.9-207.9,442z">
                        </path>
                    </defs>
                </svg>
            </figure>
        </div>
        <div class="content-wrapper">
            <div class="content">
                <div class="login">
                    <img v-if="sys.logo_url" :src="sys.logo_url" class="login-logo" alt="Logo">
                    <h1>Welcome Back👋🏻</h1>
                    <div class="subtitle">Enter your account details to participate survey</div>
                    <form class="el-form login-form el-form--label-left" auto-complete="off">
                        <div class="item" style="margin-top: 20px;">
                            <div class="field-label">Account</div>
                            <el-input v-model="account" autocomplete="off" class="w-50 m-2" size="large" />
                        </div>
                        <div class="item" style="margin-top: 30px;">
                            <div class="field-label">Password</div>
                            <el-input v-model="password" type="password" autocomplete="off" class="w-50 m-2"
                                size="large" />
                        </div>
                        <div class="item forgot">
                            <a>Forgot your password?</a>
                        </div>

                        <div class="login-btn" @click="startLogin">
                            Sign In
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useRoute, useRouter } from 'vue-router'
import { login } from '@/api/modules/user';
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
import { ElMessage } from "element-plus";
import { onMounted, ref, watch } from "vue";
const route = useRoute()
const router = useRouter()
const { token, sys } = storeToRefs(useUserStore())
const account = ref()
const password = ref()
const canvas = ref(null);
const ctx = ref(null);
const animationFrame = ref(null);

const starDensity = ref(12000);
const twinkleSpeed = ref(5);
const shootingStarFrequency = ref(8);
const starColor = ref("#ffffff");
const shootingStarColor = ref("#ffffff");

const stars = ref([]);
const shootingStars = ref([]);

const initCanvas = () => {
    if (!canvas.value) return;
    canvas.value.width = window.innerWidth;
    canvas.value.height = window.innerHeight;
    ctx.value = canvas.value.getContext('2d');
    createStars();
};

const createStars = () => {
    stars.value = [];
    const count = Math.floor((canvas.value.width * canvas.value.height) / starDensity.value);
    for (let i = 0; i < count; i++) {
        stars.value.push({
            x: Math.random() * canvas.value.width,
            y: Math.random() * canvas.value.height,
            radius: Math.random() * 1.5 + 0.5,
            opacity: Math.random(),
            blinkSpeed: Math.random() * 0.05 + 0.005,
            blinkDirection: Math.random() > 0.5 ? 1 : -1
        });
    }
};

const createShootingStar = () => {
    const length = Math.random() * 100 + 50;
    const angle = Math.random() * Math.PI / 4 + Math.PI / 8;
    shootingStars.value.push({
        x: Math.random() * canvas.value.width,
        y: Math.random() * canvas.value.height / 4,
        length: length,
        angle: angle,
        speed: Math.random() * 10 + 5,
        opacity: 1,
        tail: []
    });
};

const drawStars = () => {
    ctx.value.clearRect(0, 0, canvas.value.width, canvas.value.height);
    stars.value.forEach(star => {
        star.opacity += star.blinkSpeed * star.blinkDirection * (twinkleSpeed.value / 5);
        if (star.opacity > 1) { star.opacity = 1; star.blinkDirection = -1; }
        else if (star.opacity < 0.3) { star.opacity = 0.3; star.blinkDirection = 1; }
        ctx.value.beginPath();
        ctx.value.arc(star.x, star.y, star.radius, 0, Math.PI * 2);
        ctx.value.fillStyle = `${starColor.value}${Math.floor(star.opacity * 255).toString(16).padStart(2, '0')}`;
        ctx.value.fill();
    });
    for (let i = shootingStars.value.length - 1; i >= 0; i--) {
        const star = shootingStars.value[i];
        star.x += Math.cos(star.angle) * star.speed;
        star.y += Math.sin(star.angle) * star.speed;
        star.tail.push({ x: star.x, y: star.y });
        if (star.tail.length > 15) star.tail.shift();
        star.opacity -= 0.01;
        ctx.value.beginPath();
        ctx.value.moveTo(star.tail[0].x, star.tail[0].y);
        for (let j = 1; j < star.tail.length; j++) {
            ctx.value.lineTo(star.tail[j].x, star.tail[j].y);
        }
        ctx.value.strokeStyle = `${shootingStarColor.value}${Math.floor(star.opacity * 255).toString(16).padStart(2, '0')}`;
        ctx.value.lineWidth = 2;
        ctx.value.lineCap = 'round';
        ctx.value.lineJoin = 'round';
        ctx.value.stroke();
        if (star.opacity <= 0) shootingStars.value.splice(i, 1);
    }
    if (Math.random() < shootingStarFrequency.value / 1000) createShootingStar();
    animationFrame.value = requestAnimationFrame(drawStars);
};

const handleResize = () => {
    if (canvas.value) {
        canvas.value.width = window.innerWidth;
        canvas.value.height = window.innerHeight;
        createStars();
    }
};

onMounted(() => {
    initCanvas();
    drawStars();
    window.addEventListener('resize', handleResize);
});

watch(starDensity, () => { createStars(); });

const cleanup = () => {
    cancelAnimationFrame(animationFrame.value);
    window.removeEventListener('resize', handleResize);
};

const startLogin = async () => {
    if (!account.value || !password.value) {
        ElMessage({ type: 'error', message: 'Invalid parameters' });
        return;
    }
    const res = await login({ account: account.value, password: password.value });
    if (res.code == 200) {
        token.value = res.data.ApiToken;
        router.push({ name: 'offers' });
    }
};
</script>

<style scoped>
:deep(input:-webkit-autofill) {
    color: white !important;
    background-color: transparent !important;
    -webkit-text-fill-color: white !important;
    transition: background-color 5000s ease-in-out 0s !important;
    -webkit-box-shadow: 0 0 0 400px transparent inset;
}

/* ── Element Plus input full-width fix ── */
:deep(.el-input) {
    width: 100% !important;
}

:deep(.el-input__wrapper) {
    background: rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 0 0 1px rgba(23, 207, 240, 0.3) inset !important;
}

:deep(.el-input__inner) {
    color: #fff !important;
    font-size: 15px;
}

/* ── Globe / SVG figure ── */
figure {
    width: 500px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    margin: 0;
}

figure #sea {
    fill: #627f9b;
}

figure #hi {
    fill: rgba(255, 255, 255, 0.2);
}

figure #light {
    fill: white;
}

figure #shad {
    fill: rgba(0, 0, 0, 0.05);
}

figure #land {
    fill: rgba(10, 92, 78, 0.85);
    animation: spin 10s linear infinite forwards;
}

figure .vehicles use {
    fill: #f0fbfc;
    animation: orbit 15s linear infinite;
    transform-origin: center center;
}

figure #boat1 {
    transform: translateX(40px) translateY(-80px);
}

figure #boat1 use {
    animation-duration: 30s;
}

figure #plane1 {
    transform: translateX(-10px) translateY(170px);
}

figure #plane2 {
    transform: translateX(170px) translateY(150px);
}

figure #plane2 use {
    animation-delay: 7.5s;
}

figure #plane3 {
    transform: translateX(140px) translateY(-50px);
}

figure #plane3 use {
    animation-delay: 1.25s;
    animation-duration: 12s;
}

figure #bike1 {
    transform: translateX(50px) translateY(190px);
}

figure #bike1 use {
    animation-duration: 25s;
}

@keyframes orbit {
    from {
        transform: rotate(0deg) translateX(-160px);
    }

    to {
        transform: rotate(360deg) translateX(-160px);
    }
}

@keyframes spin {
    from {
        transform: translateX(-300px);
    }

    to {
        transform: translateX(0px);
    }
}

/* ── Background globe container ── */
#container {
    width: 100vw;
    height: 100vh;
    background: transparent;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1;
    pointer-events: none;
}

.stars-canvas {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    z-index: 0;
}

/* ── Content panel wrapper (handles centering on mobile) ── */
.content-wrapper {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    width: 420px;
    display: flex;
    align-items: center;
    z-index: 10;
}

/* ── Content panel ── */
.content {
    width: 100%;
    height: 100%;
    padding: 60px 48px;
    background: #111;
    box-shadow: 0 0 5px rgba(64, 209, 185, 0.85);
    display: flex;
    align-items: center;
    overflow-y: auto;
    box-sizing: border-box;
}

.login {
    width: 100%;
}

.login h1 {
    font-size: 28px;
    margin: 0 0 8px;
    line-height: 1.2;
}

.login-logo {
    height: 50px;
    margin-bottom: 30px;
    display: block;
}

.subtitle {
    padding: 0 0 20px;
    color: rgba(255, 255, 255, 0.65);
    font-size: 14px;
    line-height: 1.5;
}

.field-label {
    margin-bottom: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.85);
}

.item {
    width: 100%;
}

.forgot {
    text-align: right;
    color: #fff;
    padding: 16px 0 32px;
    font-size: 13px;
}

.forgot a {
    cursor: pointer;
    opacity: 0.75;
}

.forgot a:hover {
    opacity: 1;
}

/* ── Sign In button ── */
.login-btn {
    position: relative;
    display: block;
    text-align: center;
    color: white;
    border: none;
    cursor: pointer;
    height: 52px;
    width: 100%;
    font-size: 18px;
    line-height: 52px;
    background: rgba(0, 255, 213, 0.15);
    animation: firefly-glow 1s ease-in-out infinite;
    user-select: none;
}

@keyframes firefly-glow {
    0% {
        background-position: 0% 50%;
    }

    50% {
        background-position: 100% 50%;
    }

    100% {
        background-position: 0% 50%;
    }
}

.login-btn::after,
.login-btn::before {
    content: "";
    position: absolute;
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    clip-path: inset(0 0 0 0);
    border: 2px solid rgba(23, 207, 240, 0.45);
    animation: animate 3s linear infinite;
}

.login-btn:hover {
    background: rgba(23, 207, 240, 0.45);
}

.login-btn::after {
    animation-delay: -1.5s;
}

@keyframes animate {

    0%,
    100% {
        clip-path: inset(0 98% 0 0);
    }

    25% {
        clip-path: inset(0 0 98% 0);
    }

    50% {
        clip-path: inset(0 0 0 98%);
    }

    75% {
        clip-path: inset(98% 0 0 0);
    }
}

/* ══════════════════════════════════════
   TABLET  (≤ 1024px)
   Panel slides over globe, slightly narrower
   ══════════════════════════════════════ */
@media (max-width: 1024px) {
    .content-wrapper {
        width: 380px;
    }

    .content {
        padding: 60px 36px;
    }

    figure {
        width: 380px;
        left: 30%;
    }
}

/* ══════════════════════════════════════
   MOBILE  (≤ 768px)
   Full-screen form, stars visible behind
   ══════════════════════════════════════ */
@media (max-width: 768px) {

    /* Show the globe as a faint background on small screens */
    #container {
        display: block;
        opacity: 0.4;
        transform: scale(0.8);
        z-index: 0;
    }

    /* Stars canvas still covers the background */
    .stars-canvas {
        z-index: -1;
    }

    /* Full-screen centered panel */
    .content-wrapper {
        width: 100%;
        left: 0;
        right: 0;
        align-items: flex-start;
        z-index: 10;
    }

    .content {
        background: rgba(17, 17, 17, 0.88);
        box-shadow: none;
        padding: 0 24px;
        align-items: flex-start;
        padding-top: env(safe-area-inset-top, 0px);
    }

    .login {
        padding: 60px 0 40px;
    }

    .login h1 {
        font-size: 26px;
    }

    .subtitle {
        font-size: 13px;
    }

    .login-btn {
        height: 50px;
        font-size: 16px;
        line-height: 50px;
    }
}

/* ══════════════════════════════════════
   SMALL MOBILE  (≤ 375px)
   ══════════════════════════════════════ */
@media (max-width: 375px) {
    .content {
        padding-left: 20px;
        padding-right: 20px;
    }

    .login h1 {
        font-size: 22px;
    }
}
</style>