<?php
/**
 * API路由
 * User: mortal
 * Date: 19-1-8
 * Time: 下午8:55
 */
use think\facade\Route;
//地理位置相关
//获取所有的省
Route::get('location/province/:ver','api/:ver.location/getProvince')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取某省的所有市
Route::get('location/city/:ver','api/:ver.location/getCity')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取某市的所有区县
Route::get('location/region/:ver','api/:ver.location/getRegion')->middleware(['jwt_parser','url_parser','jwt_clear']);
//解析location_id
Route::get('location/location/parse/:ver','api/:ver.location/getLocationInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定地区的人，根据点赞数排序,排序方式可以自己传入
Route::get('location/search/user/nearby/:ver','api/:ver.User/getUserInArea')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定地区的动态，根据时间排序以及热度排序，排序方式可以自己传入
Route::get('location/search/user/dynamic/nearby/:ver','api/:ver.UserDynamic/getUserDynamicInArea')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定地区内的所有商品信息
Route::get('location/search/goods/nearby/:ver','api/:ver.Goods/getGoodsInArea')->middleware(['jwt_parser','url_parser','jwt_clear']);

//用户登录相关
Route::get('login/:ver','api/:ver.login/login')->middleware('url_parser');
Route::get('logout/:ver','api/:ver.login/logout')->middleware(['jwt_parser','jwt_clear']);

//用户主页相关
//用户主页
Route::get('user/info/:ver','api/:ver.User/userInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//修改用户信息
Route::put('user/edit/:ver','api/:ver.User/updateMyInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户关注信息
//获取某用户关注的用户信息
Route::get('user/concerned/get/:ver','api/:ver.UserConcerned/getConcernedUser')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取关注某用户的用户信息
Route::get('user/concern/get/:ver','api/:ver.UserFavor/getConcernUser')->middleware(['jwt_parser','url_parser','jwt_clear']);
//读取关注状态
Route::get('user/concerned/status/:ver','api/:ver.UserConcerned/readConcern')->middleware(['jwt_parser','url_parser','jwt_clear']);
//关注或者取消关注
Route::post('user/concerned/:ver','api/:ver.UserConcerned/concern')->middleware(['jwt_parser','url_parser','jwt_clear']);
//读取用户主页点赞状态
Route::get('user/favor/status/:ver','api/:ver.UserFavor/readFavor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//点赞或者取消点赞
Route::post('user/favor/:ver','api/:ver.UserFavor/favor')->middleware(['jwt_parser','url_parser','jwt_clear']);

//用户动态相关
//获取用户的所有动态
Route::get('user/dynamic/info/:ver','api/:ver.UserDynamic/userDynamicInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定条数动态列表
Route::get('dynamic/list/info/:ver','api/:ver.UserDynamic/dynamicListInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//发布用户动态
Route::post('user/dynamic/post/:ver','api/:ver.UserDynamic/dynamicPost')->middleware(['jwt_parser','url_parser','jwt_clear']);
//修改用户动态
Route::post('user/dynamic/edit/:ver','api/:ver.UserDynamic/dynamicEdit')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户删除动态
Route::post('user/dynamic/delete/:ver','api/:ver.UserDynamic/dynamicDelete')->middleware(['jwt_parser','url_parser','jwt_clear']);
//查看用户对某动态的点赞状态
Route::get('user/dynamic/favor/status/:ver','api/:ver.UserDynamicFavor/readFavor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户给动态点赞
Route::post('user/dynamic/favor/:ver','api/:ver.UserDynamicFavor/favor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//删除一条用户回复
Route::post('user/dynamic/reply/delete/:ver','api/:ver.UserDynamicReply/userDynamicReplyDelete')->middleware(['jwt_parser','url_parser','jwt_clear']);
//添加一条用户回复
Route::post('user/dynamic/reply/add/:ver','api/:ver.UserDynamicReply/userDynamicReply')->middleware(['jwt_parser','url_parser','jwt_clear']);
//查看动态的评论点赞
Route::get('user/dynamic/reply/favor/status/:ver','api/:ver.UserDynamicReplyFavor/readFavor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户给评论点赞或者取消点赞
Route::post('user/dynamic/reply/favor/:ver','api/:ver.UserDynamicReplyFavor/favor')->middleware(['jwt_parser','url_parser','jwt_clear']);

//商品相关
//用户发布商品
Route::post('goods/post/:ver','api/:ver.Goods/goodsPost')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户修改商品信息
Route::post('goods/edit/:ver','api/:ver.Goods/goodsEdit')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户删除商品
Route::post('goods/delete/:ver','api/:ver.Goods/goodsDelete')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户操作商品
Route::post('goods/operating/:ver','api/:ver.Goods/goodsOperating')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定状态的商品
Route::get('goods/by/status/get/:ver','api/:ver.Goods/getStatusGoods')->middleware(['jwt_parser','url_parser','jwt_clear']);

//获取某用户发布的所有商品简略信息
Route::get('goods/user/info/:ver','api/:ver.Goods/goodsInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取一个商品的详细信息
Route::get('goods/one/info/:ver','api/:ver.Goods/getOneGoods')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取所有的商品信息
Route::get('goods/all/info/:ver','api/:ver.Goods/getAllGoodsInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//商品点赞或者取消点赞
Route::post('goods/favor/:ver','api/:ver.GoodsFavor/favor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//查看用户对某商品的点赞状态
Route::get('goods/favor/status/:ver','api/:ver.GoodsFavor/readFavor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//删除一条用户回复
Route::post('goods/comments/delete/:ver','api/:ver.GoodsComments/goodsCommentsDelete')->middleware(['jwt_parser','url_parser','jwt_clear']);
//添加一条用户回复
Route::post('goods/comments/add/:ver','api/:ver.GoodsComments/goodsComments')->middleware(['jwt_parser','url_parser','jwt_clear']);
//查看商品的评论点赞
Route::get('goods/comments/favor/status/:ver','api/:ver.GoodsCommentsFavor/readFavor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户给商品点赞或者取消点赞
Route::post('goods/comments/favor/:ver','api/:ver.GoodsCommentsFavor/favor')->middleware(['jwt_parser','url_parser','jwt_clear']);
//收藏
//读取对特定商品的收藏状态
Route::get('goods/collection/status/:ver','api/:ver.GoodsCollection/readCollection')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取某用户所收藏的商品信息
Route::get('goods/collected/get/:ver','api/:ver.GoodsCollection/getCollectedGoods')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取收藏某商品的用户信息
Route::get('goods/collection/get/:ver','api/:ver.GoodsCollection/getCollectionUser')->middleware(['jwt_parser','url_parser','jwt_clear']);
//收藏或者取消收藏
Route::post('goods/collect/:ver','api/:ver.GoodsCollection/collect')->middleware(['jwt_parser','url_parser','jwt_clear']);

//类别相关
//获取第一级类别
Route::get('category/first/get/:ver','api/:ver.Category/getFirstCategory')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取任一类别下的子类别
Route::get('category/child/get/:ver','api/:ver.Category/getChild')->middleware(['jwt_parser','url_parser','jwt_clear']);
//解析category_id,可以获得所有父类别
Route::get('category/id/parse/:ver','api/:ver.Category/getCategoryInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//新增类别
Route::post('category/add/:ver','api/:ver.Category/getCategoryInfo')->middleware(['jwt_parser','url_parser','jwt_clear']);
//获取特定类别下的商品
Route::get('category/goods/get/:ver','api/:ver.Goods/getCategoryGoods')->middleware(['jwt_parser','url_parser','jwt_clear']);

//搜索相关
//商品搜索
Route::get('search/goods/:ver','api/:ver.Search/searchGoods')->middleware(['jwt_parser','url_parser','jwt_clear']);
//用户搜索
Route::get('search/user/:ver','api/:ver.Search/searchUser')->middleware(['jwt_parser','url_parser','jwt_clear']);