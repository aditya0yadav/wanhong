<template>
    <main class="ranking">
        <div style="display: flex;justify-content: center;margin-bottom:120px;position: relative;z-index:2;">
            <div>
                <div class="types">
                    <div class="item" :class="type == 1 ? 'active' : ''" @click="changeType(1)">Daily</div>
                    <div class="item" :class="type == 2 ? 'active' : ''" @click="changeType(2)">monthly</div>
                </div>
                <div style="
    width: 508px;
    background: var(--t-background-color);
    padding: 42px 80px 18px;
    border-radius: 80px;
    box-shadow: rgba(0, 0, 0, 0.2) 2px 2px 7px 0px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    -webkit-box-pack: center;
    justify-content: center;
    -webkit-box-align: center;
    align-items: center;
    position: absolute;margin-left: -254px;left:50%;top:20px;">LEADERBOARD</div>
            </div>
        </div>
        <div class="rank-header">
            <div class="second" :class="theme == 'light' ? 'light' : 'dark'">
                <div class="topbox">
                    <div class="imgbox" @click="open(userData[1])">
                        <a class="avatar">
                            <img :src="userData[1] ? (userData[1].avatar ? userData[1].avatar : '') : noImg" />
                        </a>
                    </div>
                    <img src="../assets/second.png" class="chakra-image">
                </div>
                <span class="chakra-text pm">{{ userData[1] ? userData[1].nickname : '--' }}</span>
                <span class="chakra-text money"><img src="../assets/icons/coin.svg" class="coin">{{ userData[1] ?
                        userData[1].total_real_payout : '--' }}</span>
                <span class="chakra-text css-14ia5f5">Complete</span>
            </div>
            <div class="first" :class="theme == 'light' ? 'light' : 'dark'">
                <div class="topbox">
                    <div class="imgbox" style="border:2px solid rgb(255, 199, 0);margin-top:-15px;"
                        @click="open(userData[0])"><a class="avatar"><img
                                :src="userData[0] ? (userData[0].avatar ? userData[0].avatar : '') : noImg" /></a>
                    </div><img src="../assets/first.png" class="chakra-image">
                </div>
                <span class="chakra-text pm">{{ userData[0] ? userData[0].nickname : '--' }}</span>
                <span class="chakra-text money"><img src="../assets/icons/coin.svg" class="coin">{{ userData[0] ?
                        userData[0].total_real_payout : '--' }}</span>
                <span class="chakra-text">Complete</span>
            </div>
            <div class="third" :class="theme == 'light' ? 'light' : 'dark'">
                <div class="topbox">
                    <div class="imgbox" style="border:2px solid rgb(231, 124, 11)" @click="open(userData[2])">
                        <a class="avatar"><img
                                :src="userData[2] ? (userData[2].avatar ? userData[2].avatar : '') : noImg" /></a>
                    </div>
                    <img src="../assets/third.png" class="chakra-image">
                </div>
                <span class="chakra-text pm">{{ userData[2] ? userData[2].nickname : '--' }}</span>
                <span class="chakra-text money"><img src="../assets/icons/coin.svg" class="coin">{{ userData[2] ?
                        userData[2].total_real_payout : '--' }}</span>
                <span class="chakra-text">Complete</span>
            </div>
        </div>
        <div style="padding:0 300px;">
            <div
                style="min-height:500px;position: relative;z-index:2;top:0px;background-color: var(--t-background-color);border:2px solid var(--t-border-color);border-radius: 20px;padding: 24px 80px;">

                <div class="ranks">
                    <div class="item">Rank</div>
                    <div class="item">User</div>
                    <div class="item">Complete</div>
                    <div class="item">Approved</div>
                </div>
                <div>
                    <div class="ranks-list" v-for="(item, index) in userData" :key="item.id">
                        <div class="rk">{{ index + 1 }}</div>
                        <div class="user">
                            <div class="imgbox" @click="open(item)">
                                <img :src="item.avatar ? item.avatar : noImg" />
                            </div>
                            <div class="nickname" @click="open(item)">{{ item.nickname }}</div>
                            <svg @click="open(item)" width="14" height="14" viewBox="0 0 14 14" fill="none"
                                xmlns="http://www.w3.org/2000/svg" style="flex: 0 0 auto;">
                                <g clip-path="url(#clip0_110_20269)">
                                    <mask id="path-1-outside-1_110_20269" maskUnits="userSpaceOnUse" x="-0.848145"
                                        y="-0.861084" width="15" height="15" fill="black">
                                        <rect fill="white" x="-0.848145" y="-0.861084" width="15" height="15"></rect>
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11.6849 1.08439C11.364 1.04934 10.9427 1.05053 10.3295 1.05326L8.81858 1.06C8.56645 1.06113 8.3612 0.857654 8.36005 0.605525C8.35895 0.353398 8.56238 0.148096 8.81456 0.146972L10.3526 0.140109C10.9319 0.137514 11.4054 0.135393 11.784 0.176741C12.1777 0.219738 12.5263 0.314432 12.8263 0.546565C12.8928 0.598095 12.956 0.653796 13.0153 0.713308C13.0732 0.771341 13.1274 0.83299 13.1776 0.897919C13.4097 1.19788 13.5044 1.54645 13.5475 1.94018C13.5888 2.3188 13.5867 2.79227 13.5841 3.37158L13.5772 4.90966C13.5761 5.16178 13.3708 5.36526 13.1186 5.36413C12.8665 5.36301 12.663 5.15771 12.6642 4.90558L12.6709 3.39471C12.6736 2.78153 12.6748 2.36023 12.6398 2.0393C12.6284 1.935 12.6137 1.84784 12.596 1.77381L6.1265 8.24332C5.9482 8.42161 5.65915 8.42161 5.48087 8.24332C5.30259 8.06504 5.30259 7.77597 5.48087 7.59768L11.9504 1.1282C11.8764 1.11046 11.7892 1.09578 11.6849 1.08439ZM6.08862 2.07753C6.53484 2.07753 6.94089 2.07753 7.30951 2.08195C7.56163 2.08497 7.76353 2.2918 7.76049 2.54391C7.75751 2.79602 7.55067 2.99795 7.29855 2.99493C6.93589 2.99058 6.53506 2.99057 6.08664 2.99057C4.93525 2.99057 4.10837 2.9912 3.46805 3.06057C2.83628 3.12902 2.4405 3.25998 2.13128 3.48464C1.91166 3.6442 1.71853 3.83734 1.55897 4.05695C1.3343 4.36618 1.20335 4.76196 1.1349 5.39373C1.06553 6.03405 1.0649 6.8609 1.0649 8.01231C1.0649 9.16372 1.06553 9.99057 1.1349 10.6309C1.20335 11.2627 1.3343 11.6585 1.55897 11.9677C1.71853 12.1873 1.91166 12.3804 2.13128 12.54C2.4405 12.7646 2.83628 12.8956 3.46805 12.964C4.10837 13.0334 4.93525 13.034 6.08664 13.034C7.23805 13.034 8.0649 13.0334 8.70525 12.964C9.33701 12.8956 9.73279 12.7646 10.042 12.54C10.2616 12.3804 10.4548 12.1873 10.6143 11.9677C10.839 11.6585 10.9699 11.2627 11.0384 10.6309C11.1078 9.99057 11.1084 9.16372 11.1084 8.01231C11.1084 7.56388 11.1084 7.16306 11.104 6.80039C11.101 6.54827 11.3029 6.34144 11.555 6.33846C11.8072 6.33541 12.014 6.53732 12.017 6.78944C12.0214 7.15806 12.0214 7.56406 12.0214 8.01024V8.03994C12.0214 9.15775 12.0214 10.034 11.9461 10.7292C11.8692 11.4387 11.7096 12.0136 11.353 12.5044C11.1371 12.8015 10.8758 13.0628 10.5787 13.2787C10.0878 13.6353 9.51305 13.7949 8.80355 13.8718C8.10837 13.9471 7.2321 13.9471 6.11425 13.9471H6.11421H6.05903H6.05899C4.94115 13.9471 4.0649 13.9471 3.36971 13.8718C2.66025 13.7949 2.08541 13.6353 1.59461 13.2787C1.29748 13.0628 1.03617 12.8015 0.820295 12.5044C0.463705 12.0136 0.304033 11.4387 0.227168 10.7292C0.151847 10.034 0.151851 9.15776 0.151856 8.0399V8.03988V7.98479V7.98477C0.151851 6.86685 0.151847 5.99059 0.227168 5.29539C0.304033 4.58593 0.463705 4.01109 0.820295 3.52028C1.03617 3.22315 1.29748 2.96185 1.59461 2.74597C2.08541 2.38938 2.66025 2.22971 3.36971 2.15284C4.06492 2.07752 4.9412 2.07752 6.05909 2.07753H6.08859H6.08862Z">
                                        </path>
                                    </mask>
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M11.6849 1.08439C11.364 1.04934 10.9427 1.05053 10.3295 1.05326L8.81858 1.06C8.56645 1.06113 8.3612 0.857654 8.36005 0.605525C8.35895 0.353398 8.56238 0.148096 8.81456 0.146972L10.3526 0.140109C10.9319 0.137514 11.4054 0.135393 11.784 0.176741C12.1777 0.219738 12.5263 0.314432 12.8263 0.546565C12.8928 0.598095 12.956 0.653796 13.0153 0.713308C13.0732 0.771341 13.1274 0.83299 13.1776 0.897919C13.4097 1.19788 13.5044 1.54645 13.5475 1.94018C13.5888 2.3188 13.5867 2.79227 13.5841 3.37158L13.5772 4.90966C13.5761 5.16178 13.3708 5.36526 13.1186 5.36413C12.8665 5.36301 12.663 5.15771 12.6642 4.90558L12.6709 3.39471C12.6736 2.78153 12.6748 2.36023 12.6398 2.0393C12.6284 1.935 12.6137 1.84784 12.596 1.77381L6.1265 8.24332C5.9482 8.42161 5.65915 8.42161 5.48087 8.24332C5.30259 8.06504 5.30259 7.77597 5.48087 7.59768L11.9504 1.1282C11.8764 1.11046 11.7892 1.09578 11.6849 1.08439ZM6.08862 2.07753C6.53484 2.07753 6.94089 2.07753 7.30951 2.08195C7.56163 2.08497 7.76353 2.2918 7.76049 2.54391C7.75751 2.79602 7.55067 2.99795 7.29855 2.99493C6.93589 2.99058 6.53506 2.99057 6.08664 2.99057C4.93525 2.99057 4.10837 2.9912 3.46805 3.06057C2.83628 3.12902 2.4405 3.25998 2.13128 3.48464C1.91166 3.6442 1.71853 3.83734 1.55897 4.05695C1.3343 4.36618 1.20335 4.76196 1.1349 5.39373C1.06553 6.03405 1.0649 6.8609 1.0649 8.01231C1.0649 9.16372 1.06553 9.99057 1.1349 10.6309C1.20335 11.2627 1.3343 11.6585 1.55897 11.9677C1.71853 12.1873 1.91166 12.3804 2.13128 12.54C2.4405 12.7646 2.83628 12.8956 3.46805 12.964C4.10837 13.0334 4.93525 13.034 6.08664 13.034C7.23805 13.034 8.0649 13.0334 8.70525 12.964C9.33701 12.8956 9.73279 12.7646 10.042 12.54C10.2616 12.3804 10.4548 12.1873 10.6143 11.9677C10.839 11.6585 10.9699 11.2627 11.0384 10.6309C11.1078 9.99057 11.1084 9.16372 11.1084 8.01231C11.1084 7.56388 11.1084 7.16306 11.104 6.80039C11.101 6.54827 11.3029 6.34144 11.555 6.33846C11.8072 6.33541 12.014 6.53732 12.017 6.78944C12.0214 7.15806 12.0214 7.56406 12.0214 8.01024V8.03994C12.0214 9.15775 12.0214 10.034 11.9461 10.7292C11.8692 11.4387 11.7096 12.0136 11.353 12.5044C11.1371 12.8015 10.8758 13.0628 10.5787 13.2787C10.0878 13.6353 9.51305 13.7949 8.80355 13.8718C8.10837 13.9471 7.2321 13.9471 6.11425 13.9471H6.11421H6.05903H6.05899C4.94115 13.9471 4.0649 13.9471 3.36971 13.8718C2.66025 13.7949 2.08541 13.6353 1.59461 13.2787C1.29748 13.0628 1.03617 12.8015 0.820295 12.5044C0.463705 12.0136 0.304033 11.4387 0.227168 10.7292C0.151847 10.034 0.151851 9.15776 0.151856 8.0399V8.03988V7.98479V7.98477C0.151851 6.86685 0.151847 5.99059 0.227168 5.29539C0.304033 4.58593 0.463705 4.01109 0.820295 3.52028C1.03617 3.22315 1.29748 2.96185 1.59461 2.74597C2.08541 2.38938 2.66025 2.22971 3.36971 2.15284C4.06492 2.07752 4.9412 2.07752 6.05909 2.07753H6.08859H6.08862Z"
                                        fill="#01D676"></path>
                                    <path
                                        d="M10.3295 1.05326L10.3289 0.932432L10.3295 1.05326ZM11.6849 1.08439L11.6717 1.20451L11.6718 1.20451L11.6849 1.08439ZM8.81858 1.06L8.81804 0.939171L8.81804 0.939171L8.81858 1.06ZM8.36005 0.605525L8.23921 0.60605V0.606079L8.36005 0.605525ZM8.81456 0.146972L8.8151 0.267804L8.81456 0.146972ZM10.3526 0.140109L10.3532 0.260941L10.3532 0.260941L10.3526 0.140109ZM11.784 0.176741L11.7709 0.29686L11.7709 0.29686L11.784 0.176741ZM12.8263 0.546565L12.9003 0.451035L12.9002 0.451004L12.8263 0.546565ZM13.0153 0.713308L12.9297 0.798588L12.9297 0.798643L13.0153 0.713308ZM13.1776 0.897919L13.2732 0.823975L13.2732 0.823939L13.1776 0.897919ZM13.5475 1.94018L13.6676 1.92706L13.6676 1.92705L13.5475 1.94018ZM13.5841 3.37158L13.4632 3.37103V3.37104L13.5841 3.37158ZM13.5772 4.90966L13.6981 4.91021V4.91019L13.5772 4.90966ZM13.1186 5.36413L13.1192 5.2433H13.1192L13.1186 5.36413ZM12.6642 4.90558L12.785 4.90613V4.90612L12.6642 4.90558ZM12.6709 3.39471L12.7918 3.39525V3.39524L12.6709 3.39471ZM12.6398 2.0393L12.5197 2.05241L12.5197 2.05242L12.6398 2.0393ZM12.596 1.77381L12.7135 1.74569L12.6633 1.53567L12.5106 1.68837L12.596 1.77381ZM6.1265 8.24332L6.21194 8.32877L6.21194 8.32877L6.1265 8.24332ZM5.48087 8.24332L5.56632 8.15788L5.56631 8.15788L5.48087 8.24332ZM5.48087 7.59768L5.39543 7.51224L5.48087 7.59768ZM11.9504 1.1282L12.0358 1.21364L12.1885 1.06099L11.9785 1.01069L11.9504 1.1282ZM7.30951 2.08195L7.31096 1.96112L7.31096 1.96112L7.30951 2.08195ZM7.76049 2.54391L7.63967 2.54245L7.63967 2.54248L7.76049 2.54391ZM7.29855 2.99493L7.3 2.8741H7.3L7.29855 2.99493ZM3.46805 3.06057L3.45504 2.94044H3.45504L3.46805 3.06057ZM2.13128 3.48464L2.06026 3.38688L2.06025 3.38688L2.13128 3.48464ZM1.55897 4.05695L1.46121 3.98593L1.46121 3.98593L1.55897 4.05695ZM1.1349 5.39373L1.25503 5.40675L1.1349 5.39373ZM1.1349 10.6309L1.01477 10.6439L1.01477 10.6439L1.1349 10.6309ZM1.55897 11.9677L1.65672 11.8966H1.65672L1.55897 11.9677ZM2.13128 12.54L2.2023 12.4422L2.2023 12.4422L2.13128 12.54ZM3.46805 12.964L3.48107 12.8439L3.48106 12.8439L3.46805 12.964ZM8.70525 12.964L8.69224 12.8439L8.69223 12.8439L8.70525 12.964ZM10.042 12.54L9.97099 12.4422L9.97098 12.4422L10.042 12.54ZM10.6143 11.9677L10.5165 11.8966L10.5165 11.8967L10.6143 11.9677ZM11.0384 10.6309L11.1585 10.6439V10.6439L11.0384 10.6309ZM11.104 6.80039L10.9832 6.80182V6.80185L11.104 6.80039ZM11.555 6.33846L11.5565 6.45928L11.5565 6.45928L11.555 6.33846ZM12.017 6.78944L11.8962 6.79087V6.79089L12.017 6.78944ZM11.9461 10.7292L12.0663 10.7422V10.7422L11.9461 10.7292ZM11.353 12.5044L11.2552 12.4333L11.2552 12.4333L11.353 12.5044ZM10.5787 13.2787L10.6497 13.3764L10.6497 13.3764L10.5787 13.2787ZM8.80355 13.8718L8.81656 13.9919H8.81657L8.80355 13.8718ZM3.36971 13.8718L3.3567 13.9919H3.3567L3.36971 13.8718ZM1.59461 13.2787L1.52358 13.3764L1.52359 13.3764L1.59461 13.2787ZM0.820295 12.5044L0.722539 12.5754L0.722542 12.5754L0.820295 12.5044ZM0.227168 10.7292L0.347299 10.7162L0.347299 10.7162L0.227168 10.7292ZM0.151856 8.0399L0.272689 8.0399V8.0399H0.151856ZM0.151856 7.98477H0.272689V7.98477L0.151856 7.98477ZM0.227168 5.29539L0.347299 5.3084H0.347299L0.227168 5.29539ZM0.820295 3.52028L0.918051 3.59131H0.918051L0.820295 3.52028ZM1.59461 2.74597L1.66563 2.84373L1.66563 2.84372L1.59461 2.74597ZM3.36971 2.15284L3.3567 2.03271H3.3567L3.36971 2.15284ZM6.05909 2.07753L6.05909 2.19836H6.05909V2.07753ZM10.33 1.1741C10.9461 1.17135 11.3593 1.17038 11.6717 1.20451L11.698 0.964268C11.3686 0.928298 10.9392 0.929712 10.3289 0.932432L10.33 1.1741ZM8.81911 1.18084L10.33 1.1741L10.3289 0.932432L8.81804 0.939171L8.81911 1.18084ZM8.23921 0.606079C8.24068 0.92491 8.50024 1.18226 8.81912 1.18084L8.81804 0.939171C8.63267 0.939999 8.48173 0.790398 8.48088 0.604971L8.23921 0.606079ZM8.81402 0.0261395C8.49509 0.0275621 8.23783 0.287211 8.23921 0.60605L8.48088 0.605C8.48007 0.419584 8.62966 0.268631 8.8151 0.267804L8.81402 0.0261395ZM10.3521 0.0192771L8.81402 0.0261395L8.8151 0.267804L10.3532 0.260941L10.3521 0.0192771ZM11.7971 0.056622C11.4102 0.0143613 10.9288 0.0166931 10.3521 0.0192771L10.3532 0.260941C10.935 0.258334 11.4006 0.256425 11.7709 0.29686L11.7971 0.056622ZM12.9002 0.451004C12.5754 0.199633 12.2017 0.100802 11.7971 0.056622L11.7709 0.29686C12.1538 0.338673 12.4772 0.429231 12.7523 0.642126L12.9002 0.451004ZM13.1009 0.628028C13.0379 0.564817 12.9709 0.505697 12.9003 0.451035L12.7523 0.642095C12.8148 0.690493 12.8741 0.742775 12.9297 0.798588L13.1009 0.628028ZM13.2732 0.823939C13.2198 0.75506 13.1623 0.689615 13.1008 0.627974L12.9297 0.798643C12.984 0.853068 13.0349 0.91092 13.0821 0.9719L13.2732 0.823939ZM13.6676 1.92705C13.6234 1.52249 13.5245 1.14879 13.2732 0.823975L13.0821 0.971864C13.2949 1.24697 13.3855 1.57042 13.4274 1.95331L13.6676 1.92705ZM13.7049 3.37213C13.7075 2.79539 13.7098 2.31405 13.6676 1.92706L13.4274 1.95329C13.4678 2.32356 13.4659 2.78915 13.4632 3.37103L13.7049 3.37213ZM13.6981 4.91019L13.7049 3.37211L13.4632 3.37104L13.4564 4.90912L13.6981 4.91019ZM13.1181 5.48496C13.437 5.48639 13.6966 5.22906 13.6981 4.91021L13.4564 4.9091C13.4556 5.09451 13.3046 5.24413 13.1192 5.2433L13.1181 5.48496ZM12.5434 4.90502C12.5419 5.2239 12.7993 5.48355 13.1181 5.48496L13.1192 5.2433C12.9338 5.24248 12.7842 5.09151 12.785 4.90613L12.5434 4.90502ZM12.5501 3.39417L12.5434 4.90504L12.785 4.90612L12.7918 3.39525L12.5501 3.39417ZM12.5197 2.05242C12.5538 2.36491 12.5528 2.77808 12.5501 3.39418L12.7918 3.39524C12.7944 2.78499 12.7959 2.35554 12.7599 2.02617L12.5197 2.05242ZM12.4785 1.80193C12.4948 1.8699 12.5087 1.95188 12.5197 2.05241L12.7599 2.02619C12.7481 1.91813 12.7327 1.82578 12.7135 1.74569L12.4785 1.80193ZM6.21194 8.32877L12.6815 1.85926L12.5106 1.68837L6.04106 8.15788L6.21194 8.32877ZM5.39543 8.32876C5.6209 8.55424 5.98646 8.55424 6.21194 8.32877L6.04106 8.15788C5.90995 8.28898 5.69741 8.28898 5.56632 8.15788L5.39543 8.32876ZM5.39543 7.51224C5.16996 7.73771 5.16996 8.10329 5.39543 8.32877L5.56631 8.15788C5.43522 8.02678 5.43522 7.81422 5.56631 7.68312L5.39543 7.51224ZM11.8649 1.04275L5.39543 7.51224L5.56631 7.68312L12.0358 1.21364L11.8649 1.04275ZM11.6718 1.20451C11.7723 1.21548 11.8543 1.22942 11.9222 1.2457L11.9785 1.01069C11.8984 0.991494 11.8061 0.976068 11.698 0.964267L11.6718 1.20451ZM7.31096 1.96112C6.94147 1.95669 6.53465 1.9567 6.08862 1.9567V2.19836C6.53503 2.19836 6.94031 2.19836 7.30806 2.20277L7.31096 1.96112ZM7.88131 2.54537C7.88516 2.22652 7.62981 1.96495 7.31096 1.96112L7.30806 2.20277C7.49345 2.205 7.6419 2.35707 7.63967 2.54245L7.88131 2.54537ZM7.2971 3.11575C7.61595 3.11958 7.87754 2.8642 7.88131 2.54534L7.63967 2.54248C7.63747 2.72784 7.4854 2.87633 7.3 2.8741L7.2971 3.11575ZM6.08664 3.11141C6.53526 3.11141 6.93531 3.11141 7.2971 3.11575L7.3 2.8741C6.93647 2.86974 6.53487 2.86974 6.08664 2.86974V3.11141ZM3.48107 3.1807C4.11336 3.1122 4.93252 3.11141 6.08664 3.11141V2.86974C4.93797 2.86974 4.10338 2.8702 3.45504 2.94044L3.48107 3.1807ZM2.2023 3.5824C2.4875 3.37519 2.85958 3.24804 3.48107 3.1807L3.45504 2.94044C2.81299 3.01 2.39351 3.14476 2.06026 3.38688L2.2023 3.5824ZM1.65672 4.12798C1.80883 3.91862 1.99294 3.73451 2.2023 3.5824L2.06025 3.38688C1.83038 3.5539 1.62823 3.75605 1.46121 3.98593L1.65672 4.12798ZM1.25503 5.40675C1.32236 4.78525 1.44952 4.41317 1.65672 4.12798L1.46121 3.98593C1.21909 4.31918 1.08433 4.73866 1.01477 5.38072L1.25503 5.40675ZM1.18573 8.01231C1.18573 6.85818 1.18653 6.03903 1.25503 5.40675L1.01477 5.38072C0.944526 6.02906 0.944066 6.86362 0.944066 8.01231H1.18573ZM1.25503 10.6179C1.18653 9.98558 1.18573 9.16644 1.18573 8.01231H0.944066C0.944066 9.16099 0.944526 9.99556 1.01477 10.6439L1.25503 10.6179ZM1.65672 11.8966C1.44952 11.6115 1.32236 11.2394 1.25503 10.6179L1.01477 10.6439C1.08433 11.286 1.21909 11.7055 1.46121 12.0387L1.65672 11.8966ZM2.2023 12.4422C1.99294 12.2901 1.80883 12.106 1.65672 11.8966L1.46121 12.0387C1.62822 12.2686 1.83038 12.4707 2.06026 12.6377L2.2023 12.4422ZM3.48106 12.8439C2.85958 12.7766 2.4875 12.6494 2.2023 12.4422L2.06025 12.6377C2.39351 12.8799 2.81298 13.0146 3.45504 13.0842L3.48106 12.8439ZM6.08664 12.9132C4.93252 12.9132 4.11336 12.9124 3.48107 12.8439L3.45504 13.0842C4.10338 13.1544 4.93797 13.1549 6.08664 13.1549V12.9132ZM8.69223 12.8439C8.05991 12.9124 7.24077 12.9132 6.08664 12.9132V13.1549C7.23532 13.1549 8.06989 13.1544 8.71826 13.0842L8.69223 12.8439ZM9.97098 12.4422C9.68579 12.6494 9.31371 12.7766 8.69224 12.8439L8.71826 13.0842C9.36031 13.0146 9.77978 12.8799 10.113 12.6377L9.97098 12.4422ZM10.5165 11.8967C10.3645 12.106 10.1803 12.2901 9.97099 12.4422L10.113 12.6377C10.3429 12.4707 10.5451 12.2686 10.7121 12.0387L10.5165 11.8967ZM10.9182 10.6179C10.8509 11.2394 10.7238 11.6115 10.5165 11.8966L10.7121 12.0387C10.9542 11.7054 11.0889 11.286 11.1585 10.6439L10.9182 10.6179ZM10.9875 8.01231C10.9875 9.16644 10.9868 9.98558 10.9182 10.6179L11.1585 10.6439C11.2288 9.99556 11.2292 9.16099 11.2292 8.01231H10.9875ZM10.9832 6.80185C10.9875 7.16364 10.9875 7.56368 10.9875 8.01231H11.2292C11.2292 7.56408 11.2292 7.16247 11.2248 6.79893L10.9832 6.80185ZM11.5536 6.21763C11.2347 6.2214 10.9794 6.483 10.9832 6.80182L11.2248 6.79896C11.2226 6.61354 11.3711 6.46147 11.5565 6.45928L11.5536 6.21763ZM12.1378 6.78801C12.134 6.46913 11.8724 6.21378 11.5536 6.21763L11.5565 6.45928C11.7419 6.45704 11.894 6.6055 11.8962 6.79087L12.1378 6.78801ZM12.1423 8.01024C12.1423 7.56425 12.1423 7.15748 12.1378 6.78798L11.8962 6.79089C11.9006 7.15865 11.9006 7.56387 11.9006 8.01024H12.1423ZM12.1423 8.03994V8.01024H11.9006V8.03994H12.1423ZM12.0663 10.7422C12.1424 10.0391 12.1423 9.15519 12.1423 8.03994H11.9006C11.9006 9.16031 11.9004 10.029 11.826 10.7162L12.0663 10.7422ZM11.4507 12.5754C11.8248 12.0606 11.9883 11.462 12.0663 10.7422L11.826 10.7162C11.7502 11.4154 11.5944 11.9666 11.2552 12.4333L11.4507 12.5754ZM10.6497 13.3764C10.9571 13.1531 11.2274 12.8827 11.4507 12.5754L11.2552 12.4333C11.0468 12.7202 10.7945 12.9725 10.5077 13.1809L10.6497 13.3764ZM8.81657 13.9919C9.53634 13.9139 10.1348 13.7505 10.6497 13.3764L10.5077 13.1809C10.0408 13.52 9.48975 13.6759 8.79053 13.7517L8.81657 13.9919ZM6.11425 14.0679C7.22954 14.0679 8.11339 14.0681 8.81656 13.9919L8.79054 13.7517C8.10334 13.8261 7.23466 13.8263 6.11425 13.8263V14.0679ZM6.11421 14.0679H6.11425V13.8263H6.11421V14.0679ZM6.05903 14.0679H6.11421V13.8263H6.05903V14.0679ZM6.05899 14.0679H6.05903V13.8263H6.05899V14.0679ZM3.3567 13.9919C4.05988 14.0681 4.94371 14.0679 6.05899 14.0679V13.8263C4.93859 13.8263 4.06993 13.8261 3.38272 13.7517L3.3567 13.9919ZM1.52359 13.3764C2.03842 13.7505 2.63696 13.9139 3.3567 13.9919L3.38273 13.7517C2.68355 13.6759 2.1324 13.52 1.66563 13.1809L1.52359 13.3764ZM0.722542 12.5754C0.945872 12.8827 1.21619 13.1531 1.52358 13.3764L1.66564 13.1809C1.37876 12.9725 1.12648 12.7202 0.918047 12.4333L0.722542 12.5754ZM0.107038 10.7422C0.185016 11.462 0.348488 12.0606 0.722539 12.5754L0.91805 12.4333C0.578922 11.9666 0.42305 11.4154 0.347299 10.7162L0.107038 10.7422ZM0.0310222 8.0399C0.0310174 9.1552 0.030851 10.0391 0.107038 10.7422L0.347299 10.7162C0.272843 10.029 0.272684 9.16032 0.272689 8.0399L0.0310222 8.0399ZM0.0310222 8.03988V8.0399H0.272689V8.03988H0.0310222ZM0.0310222 7.98479V8.03988H0.272689V7.98479H0.0310222ZM0.0310222 7.98477V7.98479H0.272689V7.98477H0.0310222ZM0.107038 5.28237C0.030851 5.98557 0.0310174 6.86941 0.0310222 7.98478L0.272689 7.98477C0.272684 6.86429 0.272843 5.99562 0.347299 5.3084L0.107038 5.28237ZM0.722538 3.44926C0.348489 3.9641 0.185017 4.56263 0.107038 5.28237L0.347299 5.3084C0.42305 4.60922 0.578921 4.05808 0.918051 3.59131L0.722538 3.44926ZM1.52359 2.64821C1.21619 2.87155 0.945871 3.14187 0.722538 3.44926L0.918051 3.59131C1.12648 3.30443 1.37876 3.05215 1.66563 2.84373L1.52359 2.64821ZM3.3567 2.03271C2.63696 2.11069 2.03842 2.27416 1.52359 2.64821L1.66563 2.84372C2.13241 2.50459 2.68355 2.34873 3.38273 2.27297L3.3567 2.03271ZM6.05909 1.9567C4.94376 1.95669 4.0599 1.95653 3.3567 2.03271L3.38273 2.27297C4.06995 2.19852 4.93864 2.19836 6.05909 2.19836L6.05909 1.9567ZM6.08859 1.9567H6.05909V2.19836H6.08859V1.9567ZM6.08862 1.9567H6.08859V2.19836H6.08862V1.9567Z"
                                        fill="#01D676" mask="url(#path-1-outside-1_110_20269)"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_110_20269">
                                        <rect width="14" height="14" fill="white"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </div>
                        <div class="prize">
                            <div class="money"><img src="../assets/icons/coin.svg" class="coin">{{
                        item.total_real_payout }}</div>
                        </div>
                        <div class="prize">
                            <div class="money"><img src="../assets/icons/coin.svg" class="coin">{{
                        item.total_success_payout }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog :close-on-press-escape="false" :close-on-click-modal="false" v-model="userVisible" width="800"
            align-center>
            <div style="padding:40px;">
                <div class="u-header">
                    <div class="avatar">
                        <img :src="activeUser.avatar" />
                    </div>
                    <div style="font-size:20px">{{ activeUser.nickname }}</div>
                    <div>{{ activeUser.jointime }}</div>
                </div>
                <div class="u-cont">
                    <div class="title"><img src="../assets/stats.svg" />Statistics</div>
                    <div class="statsBoxes">
                        <div class="statCard">
                            <div class="cardText">
                                <div class="cardValue">{{ activeUser.offers }}</div>
                                <div class="cardValueDescription">Offers Completed</div>
                            </div>
                        </div>
                        <div class="statCard">
                            <div class="cardText">
                                <div class="cardValue reward">
                                    <div class="flex items-center " style="gap: 0.5ch">
                                        <div class="money"><img src="../assets/icons/coin.svg" class="coin">{{
                        activeUser.today_rewards }}</div>

                                    </div>
                                </div>
                                <div class="cardValueDescription">Today completed</div>
                            </div>
                        </div>
                        <div class="statCard">
                            <div class="cardText">
                                <div class="cardValue reward">
                                    <div class="flex items-center " style="gap: 0.5ch" x-data="currencyText('479650')">
                                        <div class="money"><img src="../assets/icons/coin.svg" class="coin">{{
                        activeUser.total_rewards }}</div>

                                    </div>
                                </div>
                                <div class="cardValueDescription">Total completed</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="u-cont" v-loading="loading">
                    <div class="title"><img src="../assets/activity.svg" /> Activity</div>
                    <table class="profileTable">
                        <thead>
                            <tr>
                                <td>Plat</td>
                                <td>Offer</td>
                                <td>Time</td>
                                <td>Status</td>
                                <td>Prize</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in userRewards">
                                <td>
                                    {{ item.setting_name }}
                                </td>
                                <td>
                                    {{ item.offer_name }}
                                </td>
                                <td>
                                    {{ item.createtime }}
                                </td>
                                <td>
                                    <span v-if="item.rstatus == 0" style="color:#ff9900">Pending</span>
                                    <span v-if="item.rstatus == 1" style="color:#01d676">Success</span>
                                    <span v-if="item.rstatus == 2" style="color:red;">Failure</span>
                                </td>
                                <td class="reward">
                                    <div class="money" v-if="item.rstatus == 1"><img src="../assets/icons/coin.svg" class="coin">{{
                        item.realpayout }}</div>
                                    <div v-else>--</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="flex justify-content-center"><el-pagination large layout="prev, pager, next"
                            :total="usertotal" :default-page-size="1" @current-change="changeCurrent" />
                    </div>
                </div>
            </div>
        </el-dialog>
    </main>
</template>
<script setup>
import noImg from '@/assets/wenhao.svg'
import { ref } from 'vue';

import { useUserStore } from '@/stores/modules/user'
import { storeToRefs } from 'pinia'
const { theme } = storeToRefs(useUserStore())
const loading = ref(false);
const userData = ref([]);
const userRewards = ref([]);
const type = ref(1);
const userVisible = ref(false);
const activeUser = ref({});
const usertotal = ref(0);
const currentPage = ref(1);
const fields = ref({
    daterange: '',
    user: '',
    status: '',
    platform: '',
});
const open = (item) => {
    if (!item) {
        return;
    }/*
    currentPage.value = 1;
    usertotal.value = 0;
    userRewards.value = [];
    activeUser.value = {};
    getUserPerformance({ id: item.user_id }).then(res => {
        activeUser.value = res.data;
        loading.value = true;
        userVisible.value = true;
        getUserRewards({ id: item.user_id, page: currentPage.value,pageSize:5, fields: fields.value }).then(res => {
            userRewards.value = res.data.data;
            usertotal.value = res.data.last_page;
            loading.value = false;
        })
    })*/

}
const changeType = (e) => {
    type.value = e;
    
}
</script>
<style scoped lang="scss">
.ranking {
    background-color: var(--t-over-background-color);
    padding-top: 50px;
    min-height: 100vh;
}

.u-header {
    display: grid;
    place-items: center;
    padding: 50px 0 30px;

    .avatar {
        display: flex;
        width: 90px;
        height: 90px;
        background-size: cover;
        border-radius: 50%;
        border: 2px solid #01d676;
        justify-content: center;
        align-items: center;
        overflow: hidden;

        img {
            width: 100%;
        }
    }
}

.u-cont {
    .profileTable {
        width: 100%;
        display: table;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 40px;
    }

    .profileTable td {
        font-weight: 500;
        font-size: 12px;
        color: #a9a9ca;
        line-break: anywhere;
        padding: 10px 15px;
        line-height: 160%;
    }

    .profileTable tbody td:nth-child(1) {
        width: 60px;
    }

    .profileTable tbody td:nth-child(2) {
        text-align: left;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .profileTable td:nth-child(2) {
        width: 40%;
    }

    .profileTable tbody tr:nth-child(odd) {
        background: var(--t-over-background-color);
    }

    .title {
        font-size: 16px;
        display: flex;
        align-items: center;

        img {
            margin-right: 10px;
        }
    }

    .statsBoxes {
        display: flex;
        justify-content: flex-start;
        gap: 15px;
        padding-bottom: 10px;
    }

    .statCard {
        font-family: "Poppins";
        font-style: normal;
        display: flex;
        justify-content: center;
        width: 235px;
        height: 70px;
        background: var(--t-over-background-color);
        border-radius: 6px;
        align-items: center;
        margin: 0.5rem 0 1rem 0;
    }

    .cardValue {
        font-weight: 700;
        font-size: 16px;
        text-align: center;
        letter-spacing: .01em;
        color: var(--t-color);
    }

    .cardValueDescription {
        font-weight: 500;
        font-size: 12px;
        text-align: center;
        color: var(--t-color);
        opacity: .6;
    }
}

.ranks {
    display: grid;
    grid-template-columns: 42px 1fr 1fr 1fr;
    margin-top: 20px;
    border-bottom: 1px solid var(--t-border-color);
    -webkit-box-align: center;
    align-items: center;
    padding-bottom: 16px;
    width: 100%;

    .item {
        padding-inline: 0px;
        text-transform: none;
        font-size: 14px;
        color: #a9a9ca;
        font-weight: 500;
    }
}

.ranks-list {
    display: grid;
    grid-template-columns: 42px 1fr 1fr 1fr;
    height: 52px;
    border-bottom: 1px solid rgb(40, 40, 51);
    -webkit-box-align: center;
    align-items: center;
    width: 100%;

    .rk {
        width: 32px;
        height: 32px;
        display: flex;
        -webkit-box-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 4px;
        font-weight: 600;
        background-color: rgba(255, 199, 0, 0.1);
    }

    .user {
        display: flex;
        -webkit-box-align: center;
        align-items: center;
        padding-right: 14px;

        svg {
            margin-left: 10px;
            cursor: pointer;
        }

        .imgbox {
            display: flex;
            position: relative;
            -webkit-box-pack: end;
            justify-content: end;
            margin-right: 8px;
            cursor: pointer;

            img {
                border-radius: 999px;
                width: 25px;
                height: 25px;
            }
        }

        .nickname {
            max-width: 300px;
            color: currentcolor;
            margin-right: 5px;
            max-width: 120px;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            cursor: pointer;
        }
    }
}

.types {
    -webkit-box-pack: start;
    justify-content: flex-start;
    flex-direction: row;
    align-items: center;
    z-index: 2;
    position: relative;
    overflow: auto;
    border-radius: 30px;
    height: 42px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    border: 1px solid var(--t-over-background-color);
    background-color: var(--t-background-color);
    scrollbar-width: none;
    padding: 0 5px;
    gap: 5px;

    .item {
        &:hover {
            color: #01d676;
        }

        &.active {
            background-color: #01d676;
            color: #fff;
        }

        cursor:pointer;
        color: rgb(169, 169, 202);
        font-weight: 600;
        width:200px;
        text-align: center;
        line-height: 32px;
        border-radius: 30px;
    }
}

.rank-header {
    display: flex;
    justify-content: center;

    .avatar {
        border-radius: 999px;
        display: block;
        width: 54px;
        height: 54px;
    }

    .second {
        display: inline-flex;
        width: 193px;
        padding: 70px 0px 20px;
        height: 60px;
        z-index: 1;
        flex-direction: column;
        -webkit-box-pack: end;
        justify-content: flex-end;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 24px 24px 0px 0px;
        background: rgb(255, 255, 255, .5);
        flex-shrink: 0;
        position: relative;
        top: 30px;

        &.light {
            &::before {
                content: "";
                position: absolute;
                inset: -2px;
                z-index: -1;
                border-radius: 24px 24px 0px 0px;
            }

            &::after {
                content: "";
                position: absolute;
                inset: 0px 0px -2px;
                z-index: -1;
                background: rgba(255, 255, 255);
                border-radius: 24px 24px 0px 0px;
            }
        }

        &.dark {
            &::before {
                content: "";
                position: absolute;
                inset: -2px;
                z-index: -1;
                background: linear-gradient(rgb(166, 166, 166) 0%, rgb(36, 36, 36) 80%);
                border-radius: 24px 24px 0px 0px;
            }

            &::after {
                content: "";
                position: absolute;
                inset: 0px 0px -2px;
                z-index: -1;
                background: rgba(30, 30, 46);
                border-radius: 24px 24px 0px 0px;
            }
        }

        .pm {
            color: transparent;
            font-weight: 600;
            background: linear-gradient(102deg, rgb(166, 166, 166) 16.73%, rgb(227, 227, 227) 42.67%, rgb(166, 166, 166) 81.59%, rgb(227, 227, 227) 163.24%) text;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;

        }
    }


    .topbox {
        position: absolute;
        top: -32px;
        display: flex;
        flex-direction: column;
        -webkit-box-align: center;
        align-items: center;
    }

    .imgbox {
        display: flex;
        width: 62px;
        height: 62px;
        padding: 6px;
        overflow: hidden;
        -webkit-box-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 68px;
        border: 2px solid rgb(166, 166, 166);
        background: rgb(30, 30, 46);
        position: relative;
        box-shadow: rgba(174, 174, 174, 0.25) 0px 1.513px 53.946px 0px, rgba(234, 134, 28, 0.25) 0px 2px 71.333px 0px;
        cursor: pointer;

        img {
            width: 100%;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 50%;
        }
    }

    .chakra-image {
        object-fit: contain;
        position: absolute;
        top: 62px;
        width: 26px;
        height: 33px;
    }

    .first {
        width: 195px;
        display: inline-flex;
        height: 70px;
        padding: 90px 0px 20px;
        z-index: 2;
        flex-direction: column;
        -webkit-box-pack: end;
        justify-content: flex-end;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 24px 24px 0px 0px;
        background: rgb(255, 255, 255, .5);
        flex-shrink: 0;
        position: relative;

        .pm {

            color: transparent;
            font-weight: 600;
            background: linear-gradient(102deg, rgb(255, 199, 0) 16.73%, rgb(255, 225, 119) 42.67%, rgb(255, 199, 0) 81.59%, rgb(255, 246, 214) 163.24%) text;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;

        }

        .avatar {
            border-radius: 999px;
            display: block;
            width: 54px;
            height: 54px;
        }

        .imgbox {
            width: 74px;
            height: 74px;
        }

        .chakra-image {
            object-fit: contain;
            position: absolute;
            top: 72px;
            width: 26px;
            height: 33px;
        }
        &.light {
            &::before {
                content: "";
                position: absolute;
                inset: -2px;
                z-index: -1;
                border-radius: 24px 24px 0px 0px;
            }

            &::after {
                content: "";
                position: absolute;
                inset: 0px 0px -2px;
                z-index: -1;
                background: rgb(255, 255, 255);
                border-radius: 24px 24px 0px 0px;
            }
        }
        &.dark{
            &:before {
            content: "";
            position: absolute;
            inset: -2px;
            z-index: -1;
            background: linear-gradient(rgb(255, 199, 0) 0%, rgb(36, 36, 36) 80%);
            border-radius: 24px 24px 0px 0px;
        }

        &:after {
            content: "";
            position: absolute;
            inset: 0px 0px -2px;
            z-index: -1;
            background: rgba(30, 30, 46);
            border-radius: 24px 24px 0px 0px;
        }
        }
        
    }

    .third {
        display: inline-flex;
        width: 193px;
        padding: 70px 0px 20px;
        height: 60px;
        z-index: 1;
        flex-direction: column;
        -webkit-box-pack: end;
        justify-content: flex-end;
        -webkit-box-align: center;
        align-items: center;
        border-radius: 24px 24px 0px 0px;
        background: rgb(255, 255, 255, .5);
        flex-shrink: 0;
        position: relative;
        top: 30px;

        &.light {
            &::before {
                content: "";
                position: absolute;
                inset: -2px;
                z-index: -1;
                border-radius: 24px 24px 0px 0px;
            }

            &::after {
                content: "";
                position: absolute;
                inset: 0px 0px -2px;
                z-index: -1;
                background: rgb(255, 255, 255);
                border-radius: 24px 24px 0px 0px;
            }
        }

        &.dark {
            &:before {
                content: "";
                position: absolute;
                inset: -2px;
                z-index: -1;
                background: linear-gradient(rgb(231, 124, 11) 0%, rgb(36, 36, 36) 80%);
                border-radius: 24px 24px 0px 0px;
            }

            &:after {
                content: "";
                position: absolute;
                inset: 0px 0px -2px;
                z-index: -1;
                background: rgba(30, 30, 46);
                border-radius: 24px 24px 0px 0px;
            }
        }


        .pm {
            color: transparent;
            font-weight: 600;
            background: linear-gradient(102deg, rgb(231, 124, 11) 16.73%, rgb(255, 204, 149) 42.67%, rgb(231, 124, 11) 81.59%, rgb(255, 204, 149) 163.24%) text;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;
        }
    }
}
</style>