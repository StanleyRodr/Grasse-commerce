# Repository Instructions

- The Vue application lives in `frontend/`; keep project planning documents in the repository root.
- Frontend stack: Vue 3, TypeScript, Vite, Pinia, Vue Router, Tailwind CSS, and `@lucide/vue`.
- Run frontend commands from `frontend/`, not the repository root.
- Install dependencies with `npm install` and start development with `npm run dev`.
- Verify the frontend with `npm run build`; the build runs `vue-tsc -b` before Vite.
- The current frontend is a mock-data prototype; do not assume Laravel API endpoints exist yet.
- Keep API access behind frontend services so mock data can later be replaced by Laravel without changing views.
- Preserve the Grasse visual direction: warm neutral palette, editorial serif headings, restrained monospace metadata, and responsive layouts.
- Do not put credentials, Stripe keys, or other secrets in Vue source files; real authentication and payment logic belong in the Laravel API.
