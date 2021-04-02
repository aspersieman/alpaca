import axios from 'axios'

import setup from '@/helpers/interceptors';

setup();

export function getMenu() {
  return axios.get("/api/v1/menus");
}
