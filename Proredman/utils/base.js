import { Config } from './config.js';
import { Token } from './token.js';

class Base {
  constructor() {
    this.baseRequestUrl = Config.restUrl;
  }

  //当noRefetch为true时，不做未授权重试机制
  request(params, noRefetch) {
    var that = this;
    var url = this.baseRequestUrl + params.url;
    if (!params.type) {
      params.type = 'get';
    }
    wx.request({
      url: url,
      data: params.data,
      method: params.type,
      header: {
        'content-type': 'application/json',
        'token': wx.getStorageSync('token')
      },
      success: function (res) {
        var code = res.statusCode.toString();
        var startChar = code.charAt(0);
        if (startChar == '2') {
          params.sCallback && params.sCallback(res.data);
        } else {
          if (code == '401') {
            //1 重新从服务器获取令牌 token.getTokenFromServer
            //2 再次调用base.request 发送请求
            if (!noRefetch) {
              that._refetch(params);
            }
          }
          if (noRefetch){
            params.eCallback && params.eCallback(res.data);
          }  
        }

      },
      fail: function (err) {

      }
    })
  }

  _refetch(params) {
    var token = new Token();
    token.getTokenFromServer((token) => {
      this.request(params, true);
    });
  }

  /*获取元素上绑定的值 */
  getDataSet(event, key) {
    return event.currentTarget.dataset[key];
  }
}
export { Base };