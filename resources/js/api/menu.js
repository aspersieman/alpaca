import axios from 'axios'

export function getMenu() {
  return axios.get("/api/v1/menus");
}
