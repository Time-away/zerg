import { Base } from '../../utils/base.js';
class Home extends Base{
  // 定义构造函数
  constructor(){
    super();
  }

  //获取首页轮播图
  getBannerData(id,callback){
    var params = {
      'url' : 'banner/'+id,
      'sCallback' : function(res){
        callback && callback(res.items);
      }
    };
    this.request(params);
  }

  //获取首页主题
  getThemeData(callback){
    var params = {
      'url': 'theme?ids=1,2,3',
      'sCallback': function (data) {
        callback && callback(data);
      }
    };
    this.request(params);   
  }

  //获取首页商品
  getProductsData(callback) {
    var params = {
      'url': 'product/recent',
      'sCallback': function (data) {
        callback && callback(data);
      }
    };
    this.request(params);
  }
}
export {Home};