import axios from 'axios';
import qs from 'qs'; // 兼容PHP获取数据

// 拼接数据到url上, 如name=melvinlin&password=123456
function params(data) {
  if (typeof data !== 'object') return false;
  let url = '';
  for (let key in data) {
    let value = data[key] != undefined ? data[key] : '';
    url += '&' + key + '=' + encodeURIComponent(value);
  }
  return url ? url.substring(1) : '';
}

function ajax({ method = 'get', url, data }) {
  return new Promise((resolve, reject) => {
    let options = {};
    if (method === 'post') {
      // axios.defaults.headers.post['content-Type'] = 'appliction/x-www-form-urlencoded';
      options = {
        method: 'post',
        url,
        data: qs.stringify(data)
      }
    } else {
      if (params(data)) {
        url += (url.indexOf('?') < 0 ? '?' : '&') + params(data);
      }

      options = {
        method: 'get',
        url: url
      }
    }

    axios(options).then((res) => {
      console.log('res: ', res);
      resolve(res);
    }).catch((err) => {
      console.log('err: ', err);
      reject(err);
    })
  })
}

export default ajax;
