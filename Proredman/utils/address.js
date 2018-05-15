import { Config } from 'config.js';
import { Base } from 'base.js';

class Address extends Base {
  constructor() {
    super();
  }

  setAddressInfo(res) {
    var province = res.provinceName || res.province,
      city = res.cityName || res.city,
      country = res.countyName || res.country,
      detail = res.detailInfo || res.detail;

    var totalDetail = city + country + detail;

    if (!this.isCenterCity(province)) {
      totalDetail = province + totalDetail;
    }
    return totalDetail;
  }

  //是否是直辖市
  isCenterCity(name) {
    var centerCitys = ['北京市', '天津市', '重庆市', '上海市'],
      flag = centerCitys.indexOf(name) >= 0;
    return flag;
  }

  /*更新保存地址 */
  submitAddress(data, callback) {
    data = this._setUpAddress(data);
    var param = {
      url: 'address',
      type: 'post',
      data: data,
      sCallback: function (res) {
        callback && callback(true, res);
      }, eCallback(res) {
        callback && callback(false, res);
      }
    };
    this.request(param);
  }

  /**
   * 获取我自己的收获地址
   */
  getAddress(callback){
    var that = this;
    var param = {
      url:'address',
      sCallback:function(res){
        if(res){
          res.totalDetail = that.setAddressInfo(res);
          callback && callback(res);
        }
      }
    }
    this.request(param);
  }

  /**
   * 转换地址字段名称  微信传过来的字段名称与数据库字段不相符
   */
  _setUpAddress(res) {
    var formData = {
      name: res.userName,
      province: res.provinceName,
      city: res.cityName,
      country: res.countyName,
      mobile: res.telNumber,
      detail: res.detailInfo
    }
    return formData;
  }

}
export { Address }