// vite.config.js
import vue from "file:///F:/PROJECT/yylAdminWeb/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import { defineConfig, loadEnv } from "file:///F:/PROJECT/yylAdminWeb/node_modules/vite/dist/node/index.js";
import { resolve } from "path";
import UnoCSS from "file:///F:/PROJECT/yylAdminWeb/node_modules/unocss/dist/vite.mjs";
import AutoImport from "file:///F:/PROJECT/yylAdminWeb/node_modules/unplugin-auto-import/dist/vite.js";
import Components from "file:///F:/PROJECT/yylAdminWeb/node_modules/unplugin-vue-components/dist/vite.js";
import { ElementPlusResolver } from "file:///F:/PROJECT/yylAdminWeb/node_modules/unplugin-vue-components/dist/resolvers.js";
import Icons from "file:///F:/PROJECT/yylAdminWeb/node_modules/unplugin-icons/dist/vite.mjs";
import IconsResolver from "file:///F:/PROJECT/yylAdminWeb/node_modules/unplugin-icons/dist/resolver.mjs";
import { createSvgIconsPlugin } from "file:///F:/PROJECT/yylAdminWeb/node_modules/vite-plugin-svg-icons/dist/index.mjs";
var __vite_injected_original_dirname = "F:\\PROJECT\\yylAdminWeb";
var pathSrc = resolve(__vite_injected_original_dirname, "src");
var vite_config_default = defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());
  return {
    base: env.VITE_APP_BASE,
    resolve: {
      alias: {
        "@": pathSrc,
        "vue-i18n": "vue-i18n/dist/vue-i18n.cjs.js"
      }
    },
    css: {
      // CSS 预处理器
      preprocessorOptions: {
        // 定义全局 SCSS 变量
        scss: {
          api: "modern-compiler",
          javascriptEnabled: true,
          additionalData: `@use "@/styles/variables.scss" as *;`
        }
      }
    },
    server: {
      host: "0.0.0.0",
      port: 9527,
      open: true,
      cors: true
    },
    plugins: [
      vue(),
      UnoCSS(),
      AutoImport({
        // 自动导入 Vue 相关函数，如：ref, reactive, toRef 等
        imports: ["vue", "vue-router", "vue-i18n", "pinia", "@vueuse/core"],
        // 自动导入 Element Plus 相关函数，如：ElMessage, ElMessageBox... (带样式)
        resolvers: [ElementPlusResolver(), IconsResolver({})],
        vueTemplate: true,
        dts: false
      }),
      Components({
        resolvers: [
          // 自动导入 Element Plus 组件
          ElementPlusResolver(),
          // 自动注册图标组件
          IconsResolver({ enabledCollections: ["ep"] })
        ],
        // 指定自定义组件位置(默认:src/components)
        dirs: ["src/components", "src/**/components"],
        // 配置文件位置 (false:关闭自动生成)
        dts: false
      }),
      Icons({
        autoInstall: true
      }),
      createSvgIconsPlugin({
        // 指定需要缓存的图标文件夹
        iconDirs: [resolve(pathSrc, "assets/icons")],
        // 指定symbolId格式
        symbolId: "icon-[dir]-[name]"
      })
    ],
    // 预加载项目必需的组件
    optimizeDeps: {
      include: [
        "vue",
        "vue-router",
        "vue-i18n",
        "pinia",
        "axios",
        "crypto-js",
        "nprogress",
        "path-to-regexp",
        "path-browserify",
        "echarts/core",
        "echarts/charts",
        "echarts/components",
        "echarts/renderers",
        "element-plus/es/components/affix/style/css",
        "element-plus/es/components/alert/style/css",
        "element-plus/es/components/avatar/style/css",
        "element-plus/es/components/breadcrumb-item/style/css",
        "element-plus/es/components/breadcrumb/style/css",
        "element-plus/es/components/button/style/css",
        "element-plus/es/components/card/style/css",
        "element-plus/es/components/cascader/style/css",
        "element-plus/es/components/checkbox-group/style/css",
        "element-plus/es/components/checkbox/style/css",
        "element-plus/es/components/col/style/css",
        "element-plus/es/components/config-provider/style/css",
        "element-plus/es/components/color-picker/style/css",
        "element-plus/es/components/date-picker/style/css",
        "element-plus/es/components/dialog/style/css",
        "element-plus/es/components/divider/style/css",
        "element-plus/es/components/dropdown-item/style/css",
        "element-plus/es/components/dropdown-menu/style/css",
        "element-plus/es/components/dropdown/style/css",
        "element-plus/es/components/descriptions/style/css",
        "element-plus/es/components/descriptions-item/style/css",
        "element-plus/es/components/empty/style/css",
        "element-plus/es/components/form-item/style/css",
        "element-plus/es/components/form/style/css",
        "element-plus/es/components/icon/style/css",
        "element-plus/es/components/image/style/css",
        "element-plus/es/components/input-number/style/css",
        "element-plus/es/components/input/style/css",
        "element-plus/es/components/link/style/css",
        "element-plus/es/components/loading/style/css",
        "element-plus/es/components/menu-item/style/css",
        "element-plus/es/components/menu/style/css",
        "element-plus/es/components/notification/style/css",
        "element-plus/es/components/option/style/css",
        "element-plus/es/components/pagination/style/css",
        "element-plus/es/components/popover/style/css",
        "element-plus/es/components/radio-button/style/css",
        "element-plus/es/components/radio-group/style/css",
        "element-plus/es/components/radio/style/css",
        "element-plus/es/components/rate/style/css",
        "element-plus/es/components/row/style/css",
        "element-plus/es/components/scrollbar/style/css",
        "element-plus/es/components/select/style/css",
        "element-plus/es/components/statistic/style/css",
        "element-plus/es/components/sub-menu/style/css",
        "element-plus/es/components/switch/style/css",
        "element-plus/es/components/tab-pane/style/css",
        "element-plus/es/components/table-column/style/css",
        "element-plus/es/components/table/style/css",
        "element-plus/es/components/tabs/style/css",
        "element-plus/es/components/tag/style/css",
        "element-plus/es/components/text/style/css",
        "element-plus/es/components/tooltip/style/css",
        "element-plus/es/components/tree-select/style/css",
        "element-plus/es/components/tree/style/css",
        "element-plus/es/components/upload/style/css",
        "element-plus/es/components/watermark/style/css",
        "element-plus/es/components/table-v2/style/css"
      ]
    },
    // 构建配置
    build: {
      outDir: env.VITE_APP_OUT_DIR,
      chunkSizeWarningLimit: 2e3,
      // 消除打包大小超过500kb警告
      minify: "terser",
      // Vite 2.6.x 以上需要配置 minify: "terser", terserOptions 才能生效
      terserOptions: {
        compress: {
          keep_infinity: true,
          // 防止 Infinity 被压缩成 1/0，这可能会导致 Chrome 上的性能问题
          drop_console: true,
          // 生产环境去除 console
          drop_debugger: true
          // 生产环境去除 debugger
        },
        format: {
          comments: false
          // 删除注释
        }
      },
      rollupOptions: {
        output: {
          // 用于从入口点创建的块的打包输出格式[name]表示文件名,[hash]表示该文件内容hash值
          entryFileNames: "js/[name].[hash].js",
          // 用于命名代码拆分时创建的共享块的输出命名
          chunkFileNames: "js/[name].[hash].js",
          // 用于输出静态资源的命名，[ext]表示文件扩展名
          assetFileNames: (assetInfo) => {
            const info = assetInfo.name.split(".");
            let extType = info[info.length - 1];
            if (/\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/i.test(assetInfo.name)) {
              extType = "media";
            } else if (/\.(png|jpe?g|gif|svg)(\?.*)?$/.test(assetInfo.name)) {
              extType = "img";
            } else if (/\.(woff2?|eot|ttf|otf)(\?.*)?$/i.test(assetInfo.name)) {
              extType = "fonts";
            }
            return `${extType}/[name].[hash].[ext]`;
          }
        }
      }
    }
  };
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCJGOlxcXFxQUk9KRUNUXFxcXHl5bEFkbWluV2ViXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ZpbGVuYW1lID0gXCJGOlxcXFxQUk9KRUNUXFxcXHl5bEFkbWluV2ViXFxcXHZpdGUuY29uZmlnLmpzXCI7Y29uc3QgX192aXRlX2luamVjdGVkX29yaWdpbmFsX2ltcG9ydF9tZXRhX3VybCA9IFwiZmlsZTovLy9GOi9QUk9KRUNUL3l5bEFkbWluV2ViL3ZpdGUuY29uZmlnLmpzXCI7aW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnXHJcbmltcG9ydCB7IGRlZmluZUNvbmZpZywgbG9hZEVudiB9IGZyb20gJ3ZpdGUnXHJcbmltcG9ydCB7IHJlc29sdmUgfSBmcm9tICdwYXRoJ1xyXG5pbXBvcnQgVW5vQ1NTIGZyb20gJ3Vub2Nzcy92aXRlJ1xyXG5pbXBvcnQgQXV0b0ltcG9ydCBmcm9tICd1bnBsdWdpbi1hdXRvLWltcG9ydC92aXRlJ1xyXG5pbXBvcnQgQ29tcG9uZW50cyBmcm9tICd1bnBsdWdpbi12dWUtY29tcG9uZW50cy92aXRlJ1xyXG5pbXBvcnQgeyBFbGVtZW50UGx1c1Jlc29sdmVyIH0gZnJvbSAndW5wbHVnaW4tdnVlLWNvbXBvbmVudHMvcmVzb2x2ZXJzJ1xyXG5pbXBvcnQgSWNvbnMgZnJvbSAndW5wbHVnaW4taWNvbnMvdml0ZSdcclxuaW1wb3J0IEljb25zUmVzb2x2ZXIgZnJvbSAndW5wbHVnaW4taWNvbnMvcmVzb2x2ZXInXHJcbmltcG9ydCB7IGNyZWF0ZVN2Z0ljb25zUGx1Z2luIH0gZnJvbSAndml0ZS1wbHVnaW4tc3ZnLWljb25zJ1xyXG5cclxuY29uc3QgcGF0aFNyYyA9IHJlc29sdmUoX19kaXJuYW1lLCAnc3JjJylcclxuZXhwb3J0IGRlZmF1bHQgZGVmaW5lQ29uZmlnKCh7IG1vZGUgfSkgPT4ge1xyXG4gIGNvbnN0IGVudiA9IGxvYWRFbnYobW9kZSwgcHJvY2Vzcy5jd2QoKSlcclxuICByZXR1cm4ge1xyXG4gICAgYmFzZTogZW52LlZJVEVfQVBQX0JBU0UsXHJcbiAgICByZXNvbHZlOiB7XHJcbiAgICAgIGFsaWFzOiB7XHJcbiAgICAgICAgJ0AnOiBwYXRoU3JjLFxyXG4gICAgICAgICd2dWUtaTE4bic6ICd2dWUtaTE4bi9kaXN0L3Z1ZS1pMThuLmNqcy5qcydcclxuICAgICAgfVxyXG4gICAgfSxcclxuICAgIGNzczoge1xyXG4gICAgICAvLyBDU1MgXHU5ODg0XHU1OTA0XHU3NDA2XHU1NjY4XHJcbiAgICAgIHByZXByb2Nlc3Nvck9wdGlvbnM6IHtcclxuICAgICAgICAvLyBcdTVCOUFcdTRFNDlcdTUxNjhcdTVDNDAgU0NTUyBcdTUzRDhcdTkxQ0ZcclxuICAgICAgICBzY3NzOiB7XHJcbiAgICAgICAgICBhcGk6ICdtb2Rlcm4tY29tcGlsZXInLFxyXG4gICAgICAgICAgamF2YXNjcmlwdEVuYWJsZWQ6IHRydWUsXHJcbiAgICAgICAgICBhZGRpdGlvbmFsRGF0YTogYEB1c2UgXCJAL3N0eWxlcy92YXJpYWJsZXMuc2Nzc1wiIGFzICo7YFxyXG4gICAgICAgIH1cclxuICAgICAgfVxyXG4gICAgfSxcclxuICAgIHNlcnZlcjoge1xyXG4gICAgICBob3N0OiAnMC4wLjAuMCcsXHJcbiAgICAgIHBvcnQ6IDk1MjcsXHJcbiAgICAgIG9wZW46IHRydWUsXHJcbiAgICAgIGNvcnM6IHRydWVcclxuICAgIH0sXHJcbiAgICBwbHVnaW5zOiBbXHJcbiAgICAgIHZ1ZSgpLFxyXG4gICAgICBVbm9DU1MoKSxcclxuICAgICAgQXV0b0ltcG9ydCh7XHJcbiAgICAgICAgLy8gXHU4MUVBXHU1MkE4XHU1QkZDXHU1MTY1IFZ1ZSBcdTc2RjhcdTUxNzNcdTUxRkRcdTY1NzBcdUZGMENcdTU5ODJcdUZGMUFyZWYsIHJlYWN0aXZlLCB0b1JlZiBcdTdCNDlcclxuICAgICAgICBpbXBvcnRzOiBbJ3Z1ZScsICd2dWUtcm91dGVyJywgJ3Z1ZS1pMThuJywgJ3BpbmlhJywgJ0B2dWV1c2UvY29yZSddLFxyXG4gICAgICAgIC8vIFx1ODFFQVx1NTJBOFx1NUJGQ1x1NTE2NSBFbGVtZW50IFBsdXMgXHU3NkY4XHU1MTczXHU1MUZEXHU2NTcwXHVGRjBDXHU1OTgyXHVGRjFBRWxNZXNzYWdlLCBFbE1lc3NhZ2VCb3guLi4gKFx1NUUyNlx1NjgzN1x1NUYwRilcclxuICAgICAgICByZXNvbHZlcnM6IFtFbGVtZW50UGx1c1Jlc29sdmVyKCksIEljb25zUmVzb2x2ZXIoe30pXSxcclxuICAgICAgICB2dWVUZW1wbGF0ZTogdHJ1ZSxcclxuICAgICAgICBkdHM6IGZhbHNlXHJcbiAgICAgIH0pLFxyXG4gICAgICBDb21wb25lbnRzKHtcclxuICAgICAgICByZXNvbHZlcnM6IFtcclxuICAgICAgICAgIC8vIFx1ODFFQVx1NTJBOFx1NUJGQ1x1NTE2NSBFbGVtZW50IFBsdXMgXHU3RUM0XHU0RUY2XHJcbiAgICAgICAgICBFbGVtZW50UGx1c1Jlc29sdmVyKCksXHJcbiAgICAgICAgICAvLyBcdTgxRUFcdTUyQThcdTZDRThcdTUxOENcdTU2RkVcdTY4MDdcdTdFQzRcdTRFRjZcclxuICAgICAgICAgIEljb25zUmVzb2x2ZXIoeyBlbmFibGVkQ29sbGVjdGlvbnM6IFsnZXAnXSB9KVxyXG4gICAgICAgIF0sXHJcbiAgICAgICAgLy8gXHU2MzA3XHU1QjlBXHU4MUVBXHU1QjlBXHU0RTQ5XHU3RUM0XHU0RUY2XHU0RjREXHU3RjZFKFx1OUVEOFx1OEJBNDpzcmMvY29tcG9uZW50cylcclxuICAgICAgICBkaXJzOiBbJ3NyYy9jb21wb25lbnRzJywgJ3NyYy8qKi9jb21wb25lbnRzJ10sXHJcbiAgICAgICAgLy8gXHU5MTREXHU3RjZFXHU2NTg3XHU0RUY2XHU0RjREXHU3RjZFIChmYWxzZTpcdTUxNzNcdTk1RURcdTgxRUFcdTUyQThcdTc1MUZcdTYyMTApXHJcbiAgICAgICAgZHRzOiBmYWxzZVxyXG4gICAgICB9KSxcclxuICAgICAgSWNvbnMoe1xyXG4gICAgICAgIGF1dG9JbnN0YWxsOiB0cnVlXHJcbiAgICAgIH0pLFxyXG4gICAgICBjcmVhdGVTdmdJY29uc1BsdWdpbih7XHJcbiAgICAgICAgLy8gXHU2MzA3XHU1QjlBXHU5NzAwXHU4OTgxXHU3RjEzXHU1QjU4XHU3Njg0XHU1NkZFXHU2ODA3XHU2NTg3XHU0RUY2XHU1OTM5XHJcbiAgICAgICAgaWNvbkRpcnM6IFtyZXNvbHZlKHBhdGhTcmMsICdhc3NldHMvaWNvbnMnKV0sXHJcbiAgICAgICAgLy8gXHU2MzA3XHU1QjlBc3ltYm9sSWRcdTY4M0NcdTVGMEZcclxuICAgICAgICBzeW1ib2xJZDogJ2ljb24tW2Rpcl0tW25hbWVdJ1xyXG4gICAgICB9KVxyXG4gICAgXSxcclxuICAgIC8vIFx1OTg4NFx1NTJBMFx1OEY3RFx1OTg3OVx1NzZFRVx1NUZDNVx1OTcwMFx1NzY4NFx1N0VDNFx1NEVGNlxyXG4gICAgb3B0aW1pemVEZXBzOiB7XHJcbiAgICAgIGluY2x1ZGU6IFtcclxuICAgICAgICAndnVlJyxcclxuICAgICAgICAndnVlLXJvdXRlcicsXHJcbiAgICAgICAgJ3Z1ZS1pMThuJyxcclxuICAgICAgICAncGluaWEnLFxyXG4gICAgICAgICdheGlvcycsXHJcbiAgICAgICAgJ2NyeXB0by1qcycsXHJcbiAgICAgICAgJ25wcm9ncmVzcycsXHJcbiAgICAgICAgJ3BhdGgtdG8tcmVnZXhwJyxcclxuICAgICAgICAncGF0aC1icm93c2VyaWZ5JyxcclxuICAgICAgICAnZWNoYXJ0cy9jb3JlJyxcclxuICAgICAgICAnZWNoYXJ0cy9jaGFydHMnLFxyXG4gICAgICAgICdlY2hhcnRzL2NvbXBvbmVudHMnLFxyXG4gICAgICAgICdlY2hhcnRzL3JlbmRlcmVycycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2FmZml4L3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2FsZXJ0L3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2F2YXRhci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9icmVhZGNydW1iLWl0ZW0vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvYnJlYWRjcnVtYi9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9idXR0b24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvY2FyZC9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9jYXNjYWRlci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9jaGVja2JveC1ncm91cC9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9jaGVja2JveC9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9jb2wvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvY29uZmlnLXByb3ZpZGVyL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2NvbG9yLXBpY2tlci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9kYXRlLXBpY2tlci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9kaWFsb2cvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvZGl2aWRlci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9kcm9wZG93bi1pdGVtL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2Ryb3Bkb3duLW1lbnUvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvZHJvcGRvd24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvZGVzY3JpcHRpb25zL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2Rlc2NyaXB0aW9ucy1pdGVtL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2VtcHR5L3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2Zvcm0taXRlbS9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9mb3JtL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2ljb24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvaW1hZ2Uvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvaW5wdXQtbnVtYmVyL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2lucHV0L3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL2xpbmsvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvbG9hZGluZy9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9tZW51LWl0ZW0vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvbWVudS9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9ub3RpZmljYXRpb24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvb3B0aW9uL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3BhZ2luYXRpb24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvcG9wb3Zlci9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9yYWRpby1idXR0b24vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvcmFkaW8tZ3JvdXAvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvcmFkaW8vc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvcmF0ZS9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9yb3cvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvc2Nyb2xsYmFyL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3NlbGVjdC9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy9zdGF0aXN0aWMvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvc3ViLW1lbnUvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvc3dpdGNoL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3RhYi1wYW5lL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3RhYmxlLWNvbHVtbi9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy90YWJsZS9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy90YWJzL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3RhZy9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy90ZXh0L3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3Rvb2x0aXAvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvdHJlZS1zZWxlY3Qvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvdHJlZS9zdHlsZS9jc3MnLFxyXG4gICAgICAgICdlbGVtZW50LXBsdXMvZXMvY29tcG9uZW50cy91cGxvYWQvc3R5bGUvY3NzJyxcclxuICAgICAgICAnZWxlbWVudC1wbHVzL2VzL2NvbXBvbmVudHMvd2F0ZXJtYXJrL3N0eWxlL2NzcycsXHJcbiAgICAgICAgJ2VsZW1lbnQtcGx1cy9lcy9jb21wb25lbnRzL3RhYmxlLXYyL3N0eWxlL2NzcydcclxuICAgICAgXVxyXG4gICAgfSxcclxuICAgIC8vIFx1Njc4NFx1NUVGQVx1OTE0RFx1N0Y2RVxyXG4gICAgYnVpbGQ6IHtcclxuICAgICAgb3V0RGlyOiBlbnYuVklURV9BUFBfT1VUX0RJUixcclxuICAgICAgY2h1bmtTaXplV2FybmluZ0xpbWl0OiAyMDAwLCAvLyBcdTZEODhcdTk2NjRcdTYyNTNcdTUzMDVcdTU5MjdcdTVDMEZcdThEODVcdThGQzc1MDBrYlx1OEI2Nlx1NTQ0QVxyXG4gICAgICBtaW5pZnk6ICd0ZXJzZXInLCAvLyBWaXRlIDIuNi54IFx1NEVFNVx1NEUwQVx1OTcwMFx1ODk4MVx1OTE0RFx1N0Y2RSBtaW5pZnk6IFwidGVyc2VyXCIsIHRlcnNlck9wdGlvbnMgXHU2MjREXHU4MEZEXHU3NTFGXHU2NTQ4XHJcbiAgICAgIHRlcnNlck9wdGlvbnM6IHtcclxuICAgICAgICBjb21wcmVzczoge1xyXG4gICAgICAgICAga2VlcF9pbmZpbml0eTogdHJ1ZSwgLy8gXHU5NjMyXHU2QjYyIEluZmluaXR5IFx1ODhBQlx1NTM4Qlx1N0YyOVx1NjIxMCAxLzBcdUZGMENcdThGRDlcdTUzRUZcdTgwRkRcdTRGMUFcdTVCRkNcdTgxRjQgQ2hyb21lIFx1NEUwQVx1NzY4NFx1NjAyN1x1ODBGRFx1OTVFRVx1OTg5OFxyXG4gICAgICAgICAgZHJvcF9jb25zb2xlOiB0cnVlLCAvLyBcdTc1MUZcdTRFQTdcdTczQUZcdTU4ODNcdTUzQkJcdTk2NjQgY29uc29sZVxyXG4gICAgICAgICAgZHJvcF9kZWJ1Z2dlcjogdHJ1ZSAvLyBcdTc1MUZcdTRFQTdcdTczQUZcdTU4ODNcdTUzQkJcdTk2NjQgZGVidWdnZXJcclxuICAgICAgICB9LFxyXG4gICAgICAgIGZvcm1hdDoge1xyXG4gICAgICAgICAgY29tbWVudHM6IGZhbHNlIC8vIFx1NTIyMFx1OTY2NFx1NkNFOFx1OTFDQVxyXG4gICAgICAgIH1cclxuICAgICAgfSxcclxuICAgICAgcm9sbHVwT3B0aW9uczoge1xyXG4gICAgICAgIG91dHB1dDoge1xyXG4gICAgICAgICAgLy8gXHU3NTI4XHU0RThFXHU0RUNFXHU1MTY1XHU1M0UzXHU3MEI5XHU1MjFCXHU1RUZBXHU3Njg0XHU1NzU3XHU3Njg0XHU2MjUzXHU1MzA1XHU4RjkzXHU1MUZBXHU2ODNDXHU1RjBGW25hbWVdXHU4ODY4XHU3OTNBXHU2NTg3XHU0RUY2XHU1NDBELFtoYXNoXVx1ODg2OFx1NzkzQVx1OEJFNVx1NjU4N1x1NEVGNlx1NTE4NVx1NUJCOWhhc2hcdTUwM0NcclxuICAgICAgICAgIGVudHJ5RmlsZU5hbWVzOiAnanMvW25hbWVdLltoYXNoXS5qcycsXHJcbiAgICAgICAgICAvLyBcdTc1MjhcdTRFOEVcdTU0N0RcdTU0MERcdTRFRTNcdTc4MDFcdTYyQzZcdTUyMDZcdTY1RjZcdTUyMUJcdTVFRkFcdTc2ODRcdTUxNzFcdTRFQUJcdTU3NTdcdTc2ODRcdThGOTNcdTUxRkFcdTU0N0RcdTU0MERcclxuICAgICAgICAgIGNodW5rRmlsZU5hbWVzOiAnanMvW25hbWVdLltoYXNoXS5qcycsXHJcbiAgICAgICAgICAvLyBcdTc1MjhcdTRFOEVcdThGOTNcdTUxRkFcdTk3NTlcdTYwMDFcdThENDRcdTZFOTBcdTc2ODRcdTU0N0RcdTU0MERcdUZGMENbZXh0XVx1ODg2OFx1NzkzQVx1NjU4N1x1NEVGNlx1NjI2OVx1NUM1NVx1NTQwRFxyXG4gICAgICAgICAgYXNzZXRGaWxlTmFtZXM6IChhc3NldEluZm8pID0+IHtcclxuICAgICAgICAgICAgY29uc3QgaW5mbyA9IGFzc2V0SW5mby5uYW1lLnNwbGl0KCcuJylcclxuICAgICAgICAgICAgbGV0IGV4dFR5cGUgPSBpbmZvW2luZm8ubGVuZ3RoIC0gMV1cclxuICAgICAgICAgICAgaWYgKC9cXC4obXA0fHdlYm18b2dnfG1wM3x3YXZ8ZmxhY3xhYWMpKFxcPy4qKT8kL2kudGVzdChhc3NldEluZm8ubmFtZSkpIHtcclxuICAgICAgICAgICAgICBleHRUeXBlID0gJ21lZGlhJ1xyXG4gICAgICAgICAgICB9IGVsc2UgaWYgKC9cXC4ocG5nfGpwZT9nfGdpZnxzdmcpKFxcPy4qKT8kLy50ZXN0KGFzc2V0SW5mby5uYW1lKSkge1xyXG4gICAgICAgICAgICAgIGV4dFR5cGUgPSAnaW1nJ1xyXG4gICAgICAgICAgICB9IGVsc2UgaWYgKC9cXC4od29mZjI/fGVvdHx0dGZ8b3RmKShcXD8uKik/JC9pLnRlc3QoYXNzZXRJbmZvLm5hbWUpKSB7XHJcbiAgICAgICAgICAgICAgZXh0VHlwZSA9ICdmb250cydcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICByZXR1cm4gYCR7ZXh0VHlwZX0vW25hbWVdLltoYXNoXS5bZXh0XWBcclxuICAgICAgICAgIH1cclxuICAgICAgICB9XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9XHJcbn0pXHJcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBMFAsT0FBTyxTQUFTO0FBQzFRLFNBQVMsY0FBYyxlQUFlO0FBQ3RDLFNBQVMsZUFBZTtBQUN4QixPQUFPLFlBQVk7QUFDbkIsT0FBTyxnQkFBZ0I7QUFDdkIsT0FBTyxnQkFBZ0I7QUFDdkIsU0FBUywyQkFBMkI7QUFDcEMsT0FBTyxXQUFXO0FBQ2xCLE9BQU8sbUJBQW1CO0FBQzFCLFNBQVMsNEJBQTRCO0FBVHJDLElBQU0sbUNBQW1DO0FBV3pDLElBQU0sVUFBVSxRQUFRLGtDQUFXLEtBQUs7QUFDeEMsSUFBTyxzQkFBUSxhQUFhLENBQUMsRUFBRSxLQUFLLE1BQU07QUFDeEMsUUFBTSxNQUFNLFFBQVEsTUFBTSxRQUFRLElBQUksQ0FBQztBQUN2QyxTQUFPO0FBQUEsSUFDTCxNQUFNLElBQUk7QUFBQSxJQUNWLFNBQVM7QUFBQSxNQUNQLE9BQU87QUFBQSxRQUNMLEtBQUs7QUFBQSxRQUNMLFlBQVk7QUFBQSxNQUNkO0FBQUEsSUFDRjtBQUFBLElBQ0EsS0FBSztBQUFBO0FBQUEsTUFFSCxxQkFBcUI7QUFBQTtBQUFBLFFBRW5CLE1BQU07QUFBQSxVQUNKLEtBQUs7QUFBQSxVQUNMLG1CQUFtQjtBQUFBLFVBQ25CLGdCQUFnQjtBQUFBLFFBQ2xCO0FBQUEsTUFDRjtBQUFBLElBQ0Y7QUFBQSxJQUNBLFFBQVE7QUFBQSxNQUNOLE1BQU07QUFBQSxNQUNOLE1BQU07QUFBQSxNQUNOLE1BQU07QUFBQSxNQUNOLE1BQU07QUFBQSxJQUNSO0FBQUEsSUFDQSxTQUFTO0FBQUEsTUFDUCxJQUFJO0FBQUEsTUFDSixPQUFPO0FBQUEsTUFDUCxXQUFXO0FBQUE7QUFBQSxRQUVULFNBQVMsQ0FBQyxPQUFPLGNBQWMsWUFBWSxTQUFTLGNBQWM7QUFBQTtBQUFBLFFBRWxFLFdBQVcsQ0FBQyxvQkFBb0IsR0FBRyxjQUFjLENBQUMsQ0FBQyxDQUFDO0FBQUEsUUFDcEQsYUFBYTtBQUFBLFFBQ2IsS0FBSztBQUFBLE1BQ1AsQ0FBQztBQUFBLE1BQ0QsV0FBVztBQUFBLFFBQ1QsV0FBVztBQUFBO0FBQUEsVUFFVCxvQkFBb0I7QUFBQTtBQUFBLFVBRXBCLGNBQWMsRUFBRSxvQkFBb0IsQ0FBQyxJQUFJLEVBQUUsQ0FBQztBQUFBLFFBQzlDO0FBQUE7QUFBQSxRQUVBLE1BQU0sQ0FBQyxrQkFBa0IsbUJBQW1CO0FBQUE7QUFBQSxRQUU1QyxLQUFLO0FBQUEsTUFDUCxDQUFDO0FBQUEsTUFDRCxNQUFNO0FBQUEsUUFDSixhQUFhO0FBQUEsTUFDZixDQUFDO0FBQUEsTUFDRCxxQkFBcUI7QUFBQTtBQUFBLFFBRW5CLFVBQVUsQ0FBQyxRQUFRLFNBQVMsY0FBYyxDQUFDO0FBQUE7QUFBQSxRQUUzQyxVQUFVO0FBQUEsTUFDWixDQUFDO0FBQUEsSUFDSDtBQUFBO0FBQUEsSUFFQSxjQUFjO0FBQUEsTUFDWixTQUFTO0FBQUEsUUFDUDtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxRQUNBO0FBQUEsUUFDQTtBQUFBLFFBQ0E7QUFBQSxNQUNGO0FBQUEsSUFDRjtBQUFBO0FBQUEsSUFFQSxPQUFPO0FBQUEsTUFDTCxRQUFRLElBQUk7QUFBQSxNQUNaLHVCQUF1QjtBQUFBO0FBQUEsTUFDdkIsUUFBUTtBQUFBO0FBQUEsTUFDUixlQUFlO0FBQUEsUUFDYixVQUFVO0FBQUEsVUFDUixlQUFlO0FBQUE7QUFBQSxVQUNmLGNBQWM7QUFBQTtBQUFBLFVBQ2QsZUFBZTtBQUFBO0FBQUEsUUFDakI7QUFBQSxRQUNBLFFBQVE7QUFBQSxVQUNOLFVBQVU7QUFBQTtBQUFBLFFBQ1o7QUFBQSxNQUNGO0FBQUEsTUFDQSxlQUFlO0FBQUEsUUFDYixRQUFRO0FBQUE7QUFBQSxVQUVOLGdCQUFnQjtBQUFBO0FBQUEsVUFFaEIsZ0JBQWdCO0FBQUE7QUFBQSxVQUVoQixnQkFBZ0IsQ0FBQyxjQUFjO0FBQzdCLGtCQUFNLE9BQU8sVUFBVSxLQUFLLE1BQU0sR0FBRztBQUNyQyxnQkFBSSxVQUFVLEtBQUssS0FBSyxTQUFTLENBQUM7QUFDbEMsZ0JBQUksNkNBQTZDLEtBQUssVUFBVSxJQUFJLEdBQUc7QUFDckUsd0JBQVU7QUFBQSxZQUNaLFdBQVcsZ0NBQWdDLEtBQUssVUFBVSxJQUFJLEdBQUc7QUFDL0Qsd0JBQVU7QUFBQSxZQUNaLFdBQVcsa0NBQWtDLEtBQUssVUFBVSxJQUFJLEdBQUc7QUFDakUsd0JBQVU7QUFBQSxZQUNaO0FBQ0EsbUJBQU8sR0FBRyxPQUFPO0FBQUEsVUFDbkI7QUFBQSxRQUNGO0FBQUEsTUFDRjtBQUFBLElBQ0Y7QUFBQSxFQUNGO0FBQ0YsQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
