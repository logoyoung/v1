import axios from 'axios';
import qs from 'qs';

function homeSearch(option) {
  const url = '/api/other/homeSearch.php';
  console.log('option',option);
  return axios.post(url,qs.stringify(option)).then((res)=> {
  	return Promise.resolve(res.data);
  });
}

export { homeSearch };