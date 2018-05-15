import { Base } from '../../utils/base.js';

class Cart extends Base {
  constructor() {
    super();
    this._storageKeyName = 'cart';
  }

  /*
   * 加入购物车
   * 如果之前没有这个商品，则直接添加一条新纪录，数量为counts
   * 如果有，则只将相应的数量 + counts
   * @params:
   * item - {obj} 商品对象
   * counts -{int} 商品数量
   */
  add(item, counts) {
    var cartData = this.getCartDataFromLocal();
    var isHasInfo = this._isHasThatOne(item.id, cartData);
    //如果缓存中不存在该商品，就添加商品到购物车
    if (isHasInfo.index == -1) {
      item.counts = counts;
      //商品是否在购物车处于选中状态
      item.selectStatus = true;
      cartData.push(item);
    } else {
      //如果缓存中存在该商品，就添加商品的数量
      cartData[isHasInfo.index].counts += counts;
    }
    wx.setStorageSync(this._storageKeyName, cartData);
  }

  //本地缓存 保存/更新
  execSetStorageSync(data) {
    wx.setStorageSync(this._storageKeyName, data);
  }

  //从本地缓存取出购物车数据 flag为true 返回选中的商品 false 返回全部商品
  getCartDataFromLocal(flag) {
    var res = wx.getStorageSync(this._storageKeyName);
    if (!res) {
      res = [];
    }
    //在下单的时候过滤不下单的商品
    if (flag) {
      var newRes = [];
      for (let i = 0; i < res.length; i++) {
        if (res[i].selectStatus) {
          newRes.push(res[i]);
        }
      }
      res = newRes;
    }
    return res; 
  }

  /** 计算购物车内商品总数量
   * flag 为true 计算选中商品的数量
   * flag 为false 计算购物车商品总数量 
   */
  getCartTotalCounts(flag) {
    var data = this.getCartDataFromLocal();
    var counts = 0;
    for (let i = 0; i < data.length; i++) {
      if (flag) {
        if (data[i].selectStatus) {
          counts += data[i].counts;
        }
      } else {
        counts += data[i].counts;
      }

    }
    return counts;
  }

  //商品是否存在于购物车中，存在返回该商品的数据以及所在数组的序号
  _isHasThatOne(id, arr) {
    var item, result = { index: -1 };
    for (let i = 0; i < arr.length; i++) {
      item = arr[i];
      if (item.id == id) {
        result = {
          index: i,
          data: item
        };
        break;
      }
    }
    return result;
  }

  /**
   * 修改商品数量
   * params:
   * id -{int} 商品ID
   * counts -{int} 数量
   */
  _changeCounts(id, counts) {
    var cartData = this.getCartDataFromLocal(),
      hasInfo = this._isHasThatOne(id, cartData);
    if (hasInfo.index != -1) {
      if (hasInfo.data.counts > 1) {
        cartData[hasInfo.index].counts += counts;
      }
    }
    //更新本地缓存
    wx.setStorageSync(this._storageKeyName, cartData);
  }

  //购物车增加商品数量
  addCounts(id) {
    this._changeCounts(id, 1);
  }

  //购物车减少商品数量
  cutCounts(id) {
    this._changeCounts(id, -1);
  }

  //购物车删除 可删除单个商品或者全部商品
  delete(ids) {
    if (!(ids instanceof Array)) {
      ids = [ids];
    }
    var cartData = this.getCartDataFromLocal();
    for (let i = 0; i < ids.length; i++) {
      var hasInfo = this._isHasThatOne(ids[i], cartData);
      if (hasInfo.index != -1) {
        cartData.splice(hasInfo.index, 1);//删除数组某项
      }
    }
    // 更新缓存
    wx.setStorageSync(this._storageKeyName, cartData);
  }
}
export { Cart };