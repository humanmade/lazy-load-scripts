import { defineConfig } from 'vite';

const SERVER_HOST = 'localhost';
const SERVER_PORT = 3010;

const PKG_DIR = '/wp-content/plugins/lazy-load-scripts';
const DIST_DIR = 'assets/dist';

// See https://vitejs.dev/config/
export default defineConfig( ( { command } ) => ( {
	base: command === 'serve' ? '/' : `${ PKG_DIR }/${ DIST_DIR }/`,
	build: {
		manifest: true,
		outDir: DIST_DIR,
		polyfillModulePreload: false,
		rollupOptions: {
			input: '/assets/src/index.js',
		},
	},
	server: {
		host: SERVER_HOST,
		origin: `http://${ SERVER_HOST }:${ SERVER_PORT }`,
		port: SERVER_PORT,
		strictPort: true,
		hmr: {
			host: SERVER_HOST,
			port: SERVER_PORT,
			protocol: 'ws',
		},
	},
} ) );
