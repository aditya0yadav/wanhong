<template>
    <div class="v2-dashboard-main-section">
        <div class="contest-page-title">🏆 Leaderboard</div>
        <div class="contest-content">
            <div class="contest-card">
                <div class="contest-img-wrap">
                    <img src="../assets/daily-list.svg" alt="Daily" />
                </div>
                <div class="contest-body">
                    <h2>Daily Offer Leaderboard</h2>
                    <p class="desc">Complete offers every day to climb the rankings.<br>Top earners win exclusive rewards!</p>
                    <button class="contest-btn" @click="turnToPage('daily')">View Rankings →</button>
                </div>
            </div>
            <div class="contest-card">
                <div class="contest-img-wrap">
                    <img src="../assets/affiliate-weekly-list.svg" alt="Weekly" />
                </div>
                <div class="contest-body">
                    <h2>Weekly Offer Leaderboard</h2>
                    <p class="desc">Your weekly earnings count toward the leaderboard.<br>The more you earn, the higher you rank!</p>
                    <button class="contest-btn" @click="turnToPage('weekly')">View Rankings →</button>
                </div>
            </div>
            <div class="contest-card">
                <div class="contest-img-wrap">
                    <img src="../assets/affiliate-monthly-list.svg" alt="Monthly" />
                </div>
                <div class="contest-body">
                    <h2>Monthly Offer Leaderboard</h2>
                    <p class="desc">Compete over the full month for the top spot.<br>Monthly champions get the biggest prizes!</p>
                    <button class="contest-btn" @click="turnToPage('monthly')">View Rankings →</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useRouter } from 'vue-router';
const router = useRouter()
const turnToPage = (name) => {
    router.push({ name: name })
}
</script><style lang="scss" scoped>
.contest-page-title {
    font-size: 22px;
    font-weight: 800;
    color: var(--t-color);
    margin-bottom: 20px;
    letter-spacing: -0.02em;
}
.contest-content { display: flex; flex-direction: column; gap: 16px; }
.contest-card {
    display: flex;
    align-items: center;
    gap: 24px;
    background: var(--t-card-color);
    border-radius: 16px;
    padding: 28px 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    border: 1px solid rgba(255,255,255,0.05);
    transition: transform 0.18s, box-shadow 0.18s;
    &:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,0.18); }
}
.contest-img-wrap { flex-shrink: 0; width: 180px; img { width: 100%; display: block; } }
.contest-body {
    flex: 1; min-width: 0;
    h2 { font-size: 20px; font-weight: 700; color: var(--t-color); margin-bottom: 10px; }
}
.desc { font-size: 14px; color: var(--t-color-1); line-height: 1.65; margin-bottom: 20px; }
.contest-btn {
    display: inline-block;
    padding: 11px 28px;
    border-radius: 999px;
    background: var(--el-color-primary, #4f6ef7);
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: opacity 0.18s, transform 0.18s;
    &:hover { opacity: 0.88; transform: scale(1.02); }
}
@media (max-width: 768px) {
    .contest-card {
        flex-direction: column;
        align-items: center;
        padding: 24px 20px;
        text-align: center;
        gap: 16px;
    }
    .contest-img-wrap { width: 130px; }
    .contest-body h2 { font-size: 18px; }
    .contest-btn { width: 100%; padding: 14px; font-size: 15px; }
}
</style>
