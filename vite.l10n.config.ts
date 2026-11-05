/*
 * Vite configuration used only by `make l10n-ignore` for the non-writing
 * dependency analysis. It reuses the regular configuration, but moves the
 * (unused) output directory away from the source root, which Vite warns about.
 */

import type { ConfigEnv, UserConfig, UserConfigExport } from 'vite';

import { mergeConfig } from 'vite';
import appConfig from './vite.config.ts';

export default async (env: ConfigEnv): Promise<UserConfig> => {
  const config: UserConfigExport = appConfig;
  const resolved = typeof config === 'function' ? await config(env) : await config;
  return mergeConfig(resolved, { build: { outDir: 'build/l10n-ignore-analysis' } });
};
