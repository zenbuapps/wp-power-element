import { fileURLToPath, URL } from 'node:url'
import { defineConfig } from 'vite'
import tsconfigPaths from 'vite-tsconfig-paths'
import alias from '@rollup/plugin-alias'
import path, { extname, relative } from 'path'
import optimizer from 'vite-plugin-optimizer'
import react from '@vitejs/plugin-react'
import { glob } from 'glob'

// import liveReload from 'vite-plugin-live-reload'

export default defineConfig({
	css: {
		preprocessorOptions: {
			scss: { api: 'modern-compiler' },
		},
		// postcss: {
		//     plugins: [
		//         tailwindcss('./tailwind.config.cjs'),
		//         postcssPrefixSelector({
		//             prefix: '#power-checkout-wc-setting-app',
		//             transform(prefix, selector, prefixedSelector, filePath) {
		//                 // 只處理 node_modules/element-plus 的 CSS
		//                 if (filePath && !filePath.includes('/power-checkout/js/src/index.css')) {
		//                     if (selector.startsWith(':root') || selector.startsWith('html') || selector.startsWith('body')) {
		//                         return selector
		//                     }
		//                     return prefixedSelector
		//                 }
		//                 console.log(filePath)
		//                 // 其他 CSS (例如 Tailwind) 不加 prefix
		//                 return selector
		//             }
		//         })
		//     ]
		// }
	},
	build: {
		target: 'esnext', // 確保輸出原生 ESM
		cssCodeSplit: true,
		copyPublicDir: false,
		emptyOutDir: true,
		minify: true,
		outDir: path.resolve(__dirname, 'js/dist'),
		rollupOptions: {
			external: [
				'@uidotdev/usehooks',
				'@refinedev/antd',
				'@refinedev/core',
				'antd',
				'antd-img-crop',
				'axios',
				'canvas-confetti',
				'currency-symbol-map',
				'jotai',
				'lodash-es',
				'nanoid',
				'query-string',
				'react-countdown',
				'react-highlight-words',
				'react-icons',
				'dayjs',
				'@blocknote/core',
				'@blocknote/react',
				'@blocknote/mantine',
				'zod',
			],
			input: Object.fromEntries(
				glob
					.sync('js/src/**/*.{ts,tsx}', {
						ignore: ['lib/**/*.stories.{ts,tsx}'],
					})
					.map((file) => [
						// The name of the entry point
						// lib/nested/foo.ts becomes nested/foo

						relative(
							'js/src',
							file.slice(0, file.length - extname(file).length),
						),

						// The absolute path to the entry file
						// lib/nested/foo.ts becomes /project/lib/nested/foo.ts

						fileURLToPath(new URL(file, import.meta.url)),
					]),
			),
			output: {
				format: 'es',
				entryFileNames: '[name].js',
				assetFileNames: (assetInfo) => {
					const ext = path.extname(assetInfo.name)
					// 從引用的模組資訊推斷路徑
					const originalFileNames = assetInfo.originalFileNames || ''

					if (
						'.css' === ext &&
						Array.isArray(originalFileNames) &&
						originalFileNames.length > 0
					) {
						const path = originalFileNames[0]
						const parts = path.split('/')
						// 提取相對於 js/src 的路徑
						const dir = parts.slice(2, -1).join('/')

						return `${dir}/index[extname]`
					}

					return '[name][extname]'
				},
			},
		},
	},
	plugins: [
		alias(),
		react(),
		tsconfigPaths(),
		optimizer({
			elementorModules:
				'const elementorModules = window.elementorModules; export default elementorModules;',
			jquery: 'const $ = window.jQuery; export { $ as default }',
			'@wordpress/element': `
    const wpElement = window.wp.element;
    export const {
      createElement,
      useState,
      useEffect,
      useRef,
      useMemo,
      useCallback,
      Fragment,
      render,
      createRoot,
      StrictMode
    } = wpElement;
    export default wpElement;
  `,
		}),
	],
	resolve: {
		alias: {
			'@': fileURLToPath(new URL('./js/src', import.meta.url)),
		},
	},
})
