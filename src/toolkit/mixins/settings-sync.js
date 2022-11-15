/**
 * @copyright Copyright (c) 2022 Claus-Justus Heine <himself@claus-justus-heine.de>
 * @author Claus-Justus Heine <himself@claus-justus-heine.de>
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

import { appName } from '../../config.js';
import { showError, showSuccess, showInfo, TOAST_PERMANENT_TIMEOUT } from '@nextcloud/dialogs';
import axios from '@nextcloud/axios';
import { generateUrl } from '@nextcloud/router';

/**
 * @param {string} settingsSection TDB.
 *
 * @param {object} storageObject TBD.
 *
 * @return {boolean} TBD.
 */
async function fetchSettings(settingsSection, storageObject) {
  if (storageObject === undefined) {
    storageObject = this;
  }
  try {
    const response = await axios.get(generateUrl('apps/' + appName + '/settings/' + settingsSection), {});
    for (const [key, value] of Object.entries(response.data)) {
      storageObject[key] = value;
    }
    return true;
  } catch (e) {
    console.info('ERROR', e);
    let message = t(appName, 'reason unknown');
    if (e.response && e.response.data && e.response.data.message) {
      message = e.response.data.message;
    }
    showError(t(appName, 'Unable to query the initial value of all settings: {message}', {
      message,
    }));
    return false;
  }
}

/**
 * @param {string} settingsKey TDB.
 *
 * @param {string} settingsSection TDB.
 *
 * @param {object} storageObject TBD.
 *
 * @return {boolean} TBD.
 */
async function fetchSetting(settingsKey, settingsSection, storageObject) {
  if (storageObject === undefined) {
    storageObject = this;
  }
  try {
    const response = await axios.get(generateUrl('apps/' + appName + '/settings/' + settingsSection + '/' + settingsKey), {});
    storageObject[settingsKey] = response.data.value;
    return true;
  } catch (e) {
    console.info('ERROR', e);
    let message = t(appName, 'reason unknown');
    if (e.response && e.response.data && e.response.data.message) {
      message = e.response.data.message;
    }
    showError(t(appName, 'Unable to query the initial value of {settingsKey}: {message}', {
      settingsKey,
      message,
    }));
    return false;
  }
}

/**
 * @param {string} settingsKey TDB.
 *
 * @param {string} settingsSection TDB.
 *
 * @return {boolean} TBD.
 */
async function saveSimpleSetting(settingsKey, settingsSection) {
  console.info('ARGUMENTS', settingsKey, arguments);
  console.info('SAVE SETTING', settingsKey, this[settingsKey]);
  const value = this[settingsKey];
  try {
    const response = await axios.post(generateUrl('apps/' + appName + '/settings/' + settingsSection + '/' + settingsKey), { value });
    console.info('RESPSONSE', response.data);
    const responseData = response.data;
    let displayValue;
    if (responseData) {
      if (responseData.newValue !== undefined) {
        this[settingsKey] = responseData.newValue;
        displayValue = this[settingsKey];
      }
      if (responseData.humanValue !== undefined) {
        const humanKey = 'human' + settingsKey[0].toUpperCase() + settingsKey.substring(1);
        this[humanKey] = responseData.humanValue;
        displayValue = this[humanKey];
      }
    }
    if (displayValue === true) {
      displayValue = t(appName, 'true');
    }
    if (displayValue && displayValue !== '') {
      showInfo(t(appName, 'Successfully set "{settingsKey}" to {value}.', { settingsKey, value: displayValue }));
    } else {
      showInfo(t(appName, 'Setting "{settingsKey}" has been unset successfully.', { settingsKey }));
    }
    return true;
  } catch (e) {
    console.info('RESPONSE', e);
    let message = t(appName, 'reason unknown');
    if (e.response && e.response.data && e.response.data.message) {
      message = e.response.data.message;
    }
    if (value) {
      showError(t(appName, 'Unable to set "{settingsKey}" to {value}: {message}.', {
        settingsKey,
        value: value === true ? t(appName, 'true') : '' + value,
        message,
      }));
    } else {
      showError(t(appName, 'Unable to unset "{settingsKey}": {message}', {
        settingsKey,
        value: this[settingsKey] || t(appName, 'false'),
        message,
      }));
    }
    this[settingsKey] = this.old[settingsKey];
    return false;
  }
}

/**
 * @param {string} value TBD.
 *
 * @param {string} settingsSection TDB.
 *
 * @param {string} settingsKey TDB.
 *
 * @param {boolean} force TDB.
 *
 * @return {boolean} TBD.
 */
async function saveConfirmedSetting(value, settingsSection, settingsKey, force) {
  const self = this;
  console.info('ARGS', arguments);
  try {
    const response = await axios.post(generateUrl('apps/' + appName + '/settings/' + settingsSection + '/' + settingsKey), { value, force });
    const responseData = response.data;
    if (responseData.status === 'unconfirmed') {
      OC.dialogs.confirm(
        responseData.feedback,
        t(appName, 'Confirmation Required'),
        function(answer) {
          if (answer) {
            self.saveConfirmedSetting(value, settingsSection, settingsKey, true);
          } else {
            showInfo(t(appName, 'Unconfirmed, reverting to old value.'));
            self.getData();
          }
        },
        true);
    } else {
      let displayValue;
      if (responseData) {
        if (responseData.newValue !== undefined) {
          this[settingsKey] = responseData.newValue;
          displayValue = this[settingsKey];
        }
        if (responseData.humanValue !== undefined) {
          const humanKey = 'human' + settingsKey[0].toUpperCase() + settingsKey.substring(1);
          console.info('HUMAN', humanKey, responseData.humanValue);
          this[humanKey] = responseData.humanValue;
          displayValue = this[humanKey];
        }
      }
      if (displayValue && displayValue !== '') {
        showSuccess(t(appName, 'Successfully set value for "{settingsKey}" to "{displayValue}"', { settingsKey, displayValue }));
      } else {
        showInfo(t(appName, 'Setting "{setting}" has been unset successfully.', { setting: settingsKey }));
      }
    }
    console.info('RESPONSE', response);
    return true;
  } catch (e) {
    let message = t(appName, 'reason unknown');
    if (e.response && e.response.data) {
      const responseData = e.response.data;
      if (Array.isArray(responseData.messages)) {
        message = responseData.messages.join(' ');
      }
      console.info('RESPONSE', e.response);
    }
    showError(t(appName, 'Could not set value for "{settingsKey}" to "{value}": {message}', { settingsKey, value, message }), { timeout: TOAST_PERMANENT_TIMEOUT });
    self.getData();
    return false;
  }
}

const mixins = {
  methods: {
    fetchSetting,
    fetchSettings,
    saveConfirmedSetting,
    saveSimpleSetting,
  },
};

export default mixins;

export {
  fetchSetting,
  fetchSettings,
  saveConfirmedSetting,
  saveSimpleSetting,
};