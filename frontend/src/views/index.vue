<template>
    <div>
        <div class="v2-index-header">
            <div class="v2-index-header-box">
                <a href="/"><img :src="sys.logo_url" class="v2-index-header-logo"></a>
                <div class="v2-index-header-box-right">
                    <div class="userinfo" v-if="userInfo.member_id">
                        <div class="uinfo">
                            <el-avatar :size="24" fit="cover" :src="userInfo.avatar_url || ava"
                                style="margin-right: 10px;" />
                            {{ userInfo.nickname }}
                            <el-icon style="margin-left: 5px">
                                <ArrowDownBold />
                            </el-icon>
                            <div class="pop" @click.stop>
                                <div class="uitem" @click="startOffers">
                                    <img src="../assets/cashout.svg" />
                                    Dashboard
                                </div>
                                <div class="uitem" @click="startLogout">
                                    <img src="../assets/cashout.svg" />
                                    Log Out
                                </div>

                            </div>
                        </div>
                    </div>
                    <div v-else class="v2-index-header-logo-right">
                        <button @click="$router.push('/login')">LOGIN</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="v2-container">
            <div class="v2-index-live">
                <p class="v2-index-live-title"><b></b><span>Live Cashout</span></p>
                <div class="v2-index-live-scroll">
                    <el-scrollbar class="hidden-scroll" ref="scrollArea" min-size="0">
                        <ul>
                            <li v-for="item in rewards">
                                <span>{{ item.platform_name }}</span>
                                <span>{{ item.member_name }}</span>
                                <span><img src="../assets/coin.svg" alt="">{{
                    item.member_payout
                }}</span>
                            </li>
                        </ul>
                    </el-scrollbar>
                </div>
            </div>
        </div>
        <div class="v2-start-earning">
            <h1>Earn rewards effortlessly</h1>
            <p class="v2-start-earning-title">Your reward, our concern.</p>
            <button @click="startOffers">Start earning now</button>
            <div class="v2-start-earning-amount-coin">
                <div>
                    <img src="../assets/amount.svg" loading="lazy" alt="">
                    <b>150+</b>
                    <i>offers availabe in</i>
                </div>
                <div>
                    <img src="../assets/coin.svg" loading="lazy" alt="">
                    <p>Earn up to <b>$150</b> per offer</p>
                </div>
            </div>
            <ul>
                <li></li>
            </ul>
            <div class="v2-start-earnin-songshu-coingift">
                <img src="../assets/coingift_v2_2_daily.png" style="display: inline;">
            </div>
        </div>
        <div class="v2-getting-start">
            <h2>Getting started is easy</h2>
            <ul>
                <li>
                    <div class="day-theme v2-getting-start-img" style="display: block;">
                        <img src="../assets/sign-up_v2.gif" loading="lazy" alt="">
                    </div>
                    <div>
                        <p>Login</p>
                        <span>Sign up a free account and login.</span>
                    </div>
                </li>
                <li>
                    <div class="day-theme v2-getting-start-img" style="display: block;">
                        <img src="../assets/earn_v2.gif" loading="lazy" alt="">
                    </div>
                    <div>
                        <p>Earn</p>
                        <span>Earn coins by browsing, gaming, trying out and sharing options.</span>
                    </div>
                </li>
                <li>
                    <div class="day-theme v2-getting-start-img" style="display: block;">
                        <img src="../assets/cashout_v2.gif" loading="lazy" alt="">
                    </div>
                    <div>
                        <p>Cashout</p>
                        <span>Exchange collected coins for cash or a variety of gift cards.</span>
                    </div>
                </li>
            </ul>
            <button @click="startOffers">Get start now</button>
        </div>
        <div class="v2-redeem-coins-num">
            <h2>Redeem your coins upon your wish</h2>
            <p>We support cash and gift cards.</p>
            <img src="../assets/redeem1.svg" alt="">
            <button @click="startOffers">Start earning right now</button>
            <ul>
                <li>
                    <img src="../assets/users.svg" alt="">
                    <div>
                        <span class="v2-redeem-coins-num-1">7,636,527</span>
                        <p>Total happy users</p>
                    </div>
                </li>
                <li>
                    <img src="../assets/redeemed.svg" alt="">
                    <div>
                        <span class="v2-redeem-coins-num-2">2,601,067</span>
                        <p>Successful payments</p>
                    </div>
                </li>
                <li>
                    <img src="../assets/establishment.svg" alt="">
                    <div>
                        <span class="v2-redeem-coins-num-3">2,160</span>
                        <p>Days since establishment</p>
                    </div>
                </li>
            </ul>
        </div>
        <div class="v2-footer">
            <div class="v2-footer-content">
                <div>
                    <div class="v2-footer-content-social">
                        <img class="logo" :src="sys.logo_url" />
                        <ul>
                            <li><img src="../assets/social-discord.svg" alt=""></li>
                            <li><img src="../assets/social-twitter.svg" alt=""></li>
                            <li><img src="../assets/social-telegram.svg" alt=""></li>
                            <li><img src="../assets/social-facebook.svg" alt=""></li>
                            <li><img src="../assets/social-youtube.svg" alt=""></li>
                        </ul>
                    </div>
                    <div class="v2-footer-content-about">
                        <ul>
                            <li>About</li>
                            <li><a href="/">FAQ</a></li>
                            <li><a href="/">Support</a></li>
                            <li><a href="/">Privacy Policy</a></li>
                            <li><a href="/">Terms of Service</a></li>
                        </ul>
                        <ul>
                            <li>Services</li>
                            <li><a href="/">Advertise</a></li>
                            <li><a href="/">Leaderboard</a></li>
                            <li><a href="/">Affiliate</a></li>
                        </ul>
                        <ul>
                            <li>Info</li>
                            <li><a to="/">Forum</a></li>
                            <li><a target="_blank" href="/">Testimonials</a></li>
                        </ul>
                    </div>
                </div>

                <div class="v2-footer-adgrand">
                    <div class="v2-footer-adgrand-left">{{sys.copyright}}</div>
                    <div class="v2-footer-adgrand-right"><img src="../assets/google_play.png" alt=""><img src="../assets/android.png" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { nextTick, ref } from 'vue'
import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia';
import { useRouter } from 'vue-router';
import { logout, getLastRewards } from '@/api/modules/user';
import { ElMessage } from "element-plus";
const { token, userInfo, sys } = storeToRefs(useUserStore())
const router = useRouter()
const startOffers = () => {
    if (token.value) {
        router.push({ name: 'offers' })
    } else {
        router.push({ name: 'login' })
    }
}
const rewards = ref([])
getLastRewards().then(res => {
    rewards.value = res.data
    stopScroll();
    nextTick(() => {
        startScroll()
    })
})
const scrollArea = ref(null)
let timer = null;
const SCROLL_SPEED = 1;
const startScroll = () => {
    if (timer) {
        clearInterval(timer);
    }
    timer = setInterval(() => {
        // 获取滚动容器
        const container = scrollArea.value.$el.querySelector('.el-scrollbar__wrap');
        // 判断是否已滚动到底部
        if (container.scrollWidth - (container.scrollLeft + container.clientWidth) <= 0) {
            // 滚动到顶部
            container.scrollLeft = 0;
        } else {
            container.scrollLeft += SCROLL_SPEED;
        }
    }, 30); // 根据需要调整滚动间隔
};

const stopScroll = () => {
    if (timer) {
        clearInterval(timer);
    }
};
const stopScrolling = () => {
    if (intervalId.value) {
        clearInterval(intervalId.value);
        intervalId.value = null;
    }
}
const startLogout = async () => {
    const res = await logout()
    if (res.code == 200) {
        ElMessage({
            type: 'success',
            message: '退出成功'
        })

        token.value = ''
        userInfo.value = {}
        router.push({
            name: 'login'
        })
    }
}
</script>

<style lang="scss" scoped>
.v2-footer {
    width: 100%;
    background: #3E342D;
    padding: 50px 20px;
    box-sizing: border-box;
    margin-top: 100px;
}

.v2-footer-content-social {
    min-width: 240px;

    .logo {
        width: 128px;
    }

    ul {
        display: flex;
        align-items: center;
        margin: 28px 0 0;

        li {
            margin-right: 10px;
            width: 38px;
            height: 38px;
            background: #6B5D53;
            border-radius: 8px;
            overflow: hidden;
            padding: 8px;
            box-sizing: border-box;

            img {
                width: 100%;
            }
        }
    }
}
.v2-footer-adgrand-right img{
    width: 170px;
    height: 51px;
    &:first-child{
        margin-right: 20px;
    }
}
.v2-footer-content {
    max-width: 1200px;
    width: 100%;
    margin: 0 auto;
}

.v2-footer-content>div {
    display: flex;
}

.v2-footer-content-about {
    display: flex;
    margin-left: 195px;
}

.v2-footer-content-about ul {
    width: 250px;
}

.v2-footer-content-about ul li {
    margin-bottom: 10px;
}

.v2-footer-content-about ul li:first-child {
    font-size: 16px;
    color: #D8C7BB;
}

.v2-footer-adgrand {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 80px;
}

.v2-footer-adgrand-left {
    font-size: 14px;
    color: #D8C7BB;
}

.v2-footer-content-about ul li a {
    font-size: 14px;
    color: #FFFFFF !important;
}

.v2-redeem-coins-num {
    max-width: 1200px;
    width: 100%;
    margin: 180px auto 0 auto;

    >h2 {
        font-family: "Lato-Heavy";
        font-size: 32px;
        color: #312B26;
        text-align: center;
    }

    >p {
        font-family: "Lato-Medium";
        font-size: 18px;
        color: #77706A;
        margin-top: 10px;
        text-align: center;
    }

    button {
        width: 311px;
        height: 58px;
        background: #FF8D32;
        border-radius: 29px;
        font-family: "Lato-Bold";
        font-size: 18px;
        color: #FFFFFF;
        border: 0;
        outline: 0;
        display: block;
        margin: 20px auto 0 auto;
        cursor: pointer;
    }

    ul {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 100px 0 0;

        li {
            width: 360px;
            height: 168px;
            background: #ffffff;
            box-shadow: 0 10px 24px rgba(17, 33, 63, 0.06);
            border-radius: 18px;
            border: 1px solid rgba(17, 33, 63, 0.06);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            text-align: center;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;

            &:hover {
                transform: translateY(-3px);
                box-shadow: 0 14px 28px rgba(17, 33, 63, 0.09);
                border-color: rgba(17, 33, 63, 0.08);
            }

            img {
                width: 80px;
                position: absolute;
                left: 50%;
                top: -50px;
                transform: translateX(-50%);
            }

            span {
                font-family: "Lato-Heavy";
                font-size: 42px;
                color: #1f2f44;
                position: relative;
                z-index: 1;
            }
        }
    }
}

.v2-getting-start {
    max-width: 1020px;
    width: 100%;
    margin: 180px auto 0 auto;
    padding: 0 20px;

    h2 {
        font-family: "Lato-Heavy";
        font-size: 32px;
        color: #312B26;
        text-align: center;
        margin-bottom: 50px;
    }

    ul {
        display: flex;
        justify-content: space-between;
        margin: 0;

        li {
            width: 252px;
            text-align: center;

            img {
                width: 132px;
                height: 132px;
                box-shadow: 0 8px 20px rgba(17, 33, 63, 0.08);
                background: #ffffff;
                border-radius: 22px;
                border: 1px solid rgba(17, 33, 63, 0.06);
                padding: 12px;
                transition: transform .2s ease, box-shadow .2s ease;

                &:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 12px 24px rgba(17, 33, 63, 0.10);
                }
            }

            p {
                font-family: "Lato-Bold";
                font-size: 24px;
                color: #312B26;
                margin: 20px 0;
            }

            span {
                font-family: "Lato-Medium";
                font-size: 14px;
                color: #312B26;
            }


        }
    }

    button {
        display: block;
        width: 258px;
        height: 48px;
        background: #FF8D32;
        border-radius: 24px;
        border: 0;
        outline: 0;
        margin: 40px auto 0 auto;
        font-family: "Lato-Bold";
        font-size: 18px;
        color: #FFFFFF;
        line-height: 48px;
        cursor: pointer;
    }
}

.v2-start-earning-amount-coin {
    display: flex;
    align-items: center;
    flex-wrap: wrap;

    >div {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

    img {
        width: 24px;
        height: 24px;
        margin-right: 6px;
    }
}

.v2-start-earning ul {
    display: flex;
    margin: 80px 0 0 0;
    min-height: 170px;
}

.v2-start-earnin-songshu-coingift {
    position: absolute;
    bottom: -100px;
    right: 40px;
    width: 50%;
    height: auto;
}

.v2-start-earning {
    max-width: 1200px;
    width: 100%;
    margin: 80px auto 0 auto;
    position: relative;
    padding: 0 20px;

    h1 {
        font-size: 64px;
        color: #312B26;
        margin: 0;
    }

    .v2-start-earning-title {
        font-size: 30px;
        color: #433F3B;
        text-align: left;
        margin: 12px 0 80px 0;
    }

    button {
        width: 260px;
        height: 58px;
        background: #FF8D32;
        border-radius: 29px;
        font-family: "Lato-Bold";
        font-size: 18px;
        color: #FFFFFF;
        border: 0;
        outline: 0;
        cursor: pointer;
        margin-bottom: 28px;
    }
}

.v2-index-live-scroll {
    overflow: hidden;
    max-width: 1200px;
    flex: 1;
    height: 60px;
    margin-left: 25px;
    position: relative;

    ul {
        display: flex;
        margin: 10px 0;
        flex-wrap: nowrap;
        white-space: nowrap;

        li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 40px;
            background: #ffffff;
            box-shadow: 0 8px 18px rgba(17, 33, 63, 0.06);
            border-radius: 12px;
            border: 1px solid rgba(17, 33, 63, 0.06);
            padding: 0 14px;
            margin-right: 10px;
            flex-shrink: 1;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;

            &:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 22px rgba(17, 33, 63, 0.09);
                border-color: rgba(17, 33, 63, 0.08);
            }

            &:last-child {
                margin: 0;
            }

            span:first-child {
                background: #eef4fb;
                padding: 5px;
                border-radius: 6px;
                font-size: 12px;
                display: flex;
                align-items: center;
                margin-right: 5px;
                color: #1f2f44;

                img {
                    width: 15px;
                    height: 15px;
                    margin-right: 5px;
                }
            }

            span:last-child {
                padding: 5px;
                border-radius: 6px;
                font-size: 12px;
                display: flex;
                align-items: center;
                margin-left: 5px;
                color: #1f2f44;

                img {
                    width: 12px;
                    height: 12px;
                    margin-right: 5px;
                }
            }
        }
    }
}

.v2-index-header {
    height: 76px;
    background-color: #fff;
    border-bottom: 1px solid #F3EFEC;
    top: 0;
    right: 0;
    left: 0;
    z-index: 100;
    padding: 0 20px;

    .v2-index-header-box {
        max-width: 1200px;
        display: flex;
        margin: 0 auto;
        align-items: center;
        justify-content: space-between;
        height: 100%;

        .v2-index-header-logo {
            height: 44px;
            cursor: pointer;
        }

        .v2-index-header-box-right {
            button {
                width: 100px;
                height: 42px;
                background: #FCF2EA;
                border-radius: 6px;
                border: 2px solid #FCF2EA;
                cursor: pointer;
                outline: none;
                font-family: Lato-Heavy;
                font-size: 14px;
                color: #ff8d32;
                margin-right: 10px;
            }
        }
    }
}

.v2-container {
    max-width: 1200px;
    margin: 0 auto;
}

.v2-index-live {
    width: 100%;
    background: #FFFCFA;
    position: sticky;
    height: 60px;
    z-index: 9;
    display: flex;
    align-items: center;

    .v2-index-live-title {
        margin: 0;
        display: flex;
        align-items: center;
        height: 50px;

        b {
            width: 8px;
            height: 8px;
            background: #35D55E;
            border-radius: 50%;
            margin-right: 10px;
        }
    }

    span {
        font-family: Lato-Bold;
        font-size: 14px;
        color: #312b26;
    }
}
</style>