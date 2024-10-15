<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('resume/assets/css/styles.css') }}">
    <title>履歷</title>
</head>

<body class="bg-[#f5f5f5]">
    <div class="container max-w-[1140px] bg-white mx-auto my-10 px-8 py-10
      grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="col-span-1 md:col-span-2 px-3">
            <h2 class="text-center text-xl font-semibold mb-1">- 作品介紹 -</h2>
            <ul class="py-5">
                <li class="w-full pb-5 border-b border-dashed">
                    <div class="aspect-video flex justify-center items-center relative">
                        <img src="{{ asset('resume/img/song-home.png') }}" alt="works photo" class="w-full h-auto">
                        <a href="https://song.sally-handmade.com" target="_blank" class="absolute inset-0"></a>
                    </div>
                    <div class="text-lg">
                        音樂播放平台 - <span class="text-base text-gray-500 font-bold">前台、後台</span>
                        <div class="space-y-2 mb-2">
                            <p>- Vue開發練習作品</p>
                            <p>- 前台提供基本音樂播放功能</p>
                            <p>- 後台可以對音樂進行管理(新增/修改/刪除)</p>
                            <p>- 前後台登入註冊、刷新token免重新登入</p>
                            <p>- infinite scroll 免換頁</p>
                            <p>- 雙平台開發</p>
                        </div>
                        <p class=" text-sm text-gray-500">Vue3、Tailwind、Howler.js、RESTfulAPI</p>
                    </div>
                    <p>前台 : <a href="https://song.sally-handmade.com" target="_blank"
                            class="underline hover:text-sky-500">
                            https://song.sally-handmade.com</a></p>
                    <p>後台 : <a href="https://song.sally-handmade.com/#/admin" target="_blank"
                            class="underline hover:text-sky-500">
                            https://song.sally-handmade.com/#/admin</a></p>
                    <p>GitHub : <a href="https://github.com/yx-chill/song" target="_blank"
                            class="underline hover:text-sky-500">
                            https://github.com/yx-chill/song
                        </a></p>
                </li>
                <li class="w-full pb-5 border-b border-dashed">
                    <div class="aspect-video flex justify-center items-center relative">
                        <img src="{{ asset('resume/img/space.png') }}" alt="works photo" class="w-full h-auto">
                        <a href="https://tranquil-badlands-51503.herokuapp.com/" target="_blank"
                            class="absolute inset-0"></a>
                    </div>
                    <div class="text-lg">
                        <h3 class="text-3xl mb-3">space-tourism-website</h3>
                        <div class="space-y-2 mb-2">
                            <p>- Frontend mentor 練習作品</p>
                            <p>- Vite + Vue3</p>
                            <p>- Tailwind、RWD響應式設計</p>
                            <p>- Heroku部署</p>
                        </div>
                        <p class=" text-sm text-gray-500">Vite、Vue3、Tailwind</p>
                    </div>
                    <p>網站 : <a href="https://tranquil-badlands-51503.herokuapp.com/" target="_blank"
                            class="underline hover:text-sky-500">
                            https://tranquil-badlands-51503.herokuapp.com/</a></p>
                    <p>GitHub : <a href="https://github.com/yx-chill/Space-tourism-website" target="_blank"
                            class="underline hover:text-sky-500">
                            https://github.com/yx-chill/Space-tourism-website
                        </a></p>
                </li>
                <li class="w-full pb-5 border-b border-dashed">
                    <div class="aspect-video flex justify-center items-center relative">
                        <img src="{{ asset('resume/img/doyoga.png') }}" alt="works photo" class="w-full h-auto">
                        <a href="https://yx-chill.github.io/doyoga" target="_blank" class="absolute inset-0"></a>
                    </div>
                    <div class="text-lg">
                        瑜珈店預約網站
                        <div class="space-y-2 mb-2 text-base">
                            <p>- 六角學院網頁切版作品</p>
                            <p>- RWD響應式設計</p>
                            <p>- Grid、Flexbox排版</p>
                            <p>- Bootstrap + Sass客製化樣式</p>
                        </div>
                        <p class=" text-sm text-gray-500">Bootstrap、Sass、RWD、Swiperjs</p>
                    </div>
                    <p>網站 : <a href="https://yx-chill.github.io/doyoga/index.html" target="_blank"
                            class="underline hover:text-sky-500">
                            https://yx-chill.github.io/doyoga/index.html</a></p>
                    <p>GitHub : <a href="https://github.com/yx-chill/doyoga" target="_blank"
                            class="underline hover:text-sky-500">
                            https://github.com/yx-chill/doyoga
                        </a></p>
                </li>
            </ul>
        </div>
        <div class="col-span-1 -order-1 md:order-1 px-3 text-center">
            <div class="name text-xl font-semibold mb-1">邱盈翔</div>
            <div class="contact space-y-2 text-sm border-b border-dashed py-5">
                <div>
                    <a href="mailto:x565619@gmail.com" class="hover:text-sky-500">
                        <i class="fa-solid fa-envelope mr-1"></i>
                        x565619@gmail.com
                    </a>
                </div>
                <div>
                    <a href="https://github.com/yx-chill" target="_blank" class="hover:text-sky-500">
                        <i class="fa-brands fa-github mr-1"></i>
                        github</a>
                </div>
                <div>
                    <a><i class="fa-solid fa-phone-flip mr-1"></i>0975-035-567</a>
                </div>
            </div>
            <div class="text-left border-b border-dashed py-5">
                <P>畢業於弘光科技大學資訊工程系，畢業後在餐飲業工作了幾年，因疫情關係決定轉職網頁前端工程師，轉職期間有參加六角學院的網頁切版班，其餘時間自學JavaScript、Vue等相關知識，
                    就學期間擔任過程式設計課程助教、也負責導師教學計畫助理，培養了負責任與細心的態度，目前持續練習新的專案開發與學習新的技術，期望能將所學貢獻於職場上。
                </P>
            </div>
            <div class="skill text-left border-b border-dashed py-5">
                <h2 class="text-center text-lg font-semibold">- 技能 -</h2>
                <ul class="space-y-2">
                    <li>
                        <h3 class="mb-2 font-semibold">Web</h3>
                        <ul class="text-[15px] space-y-2">
                            <li>- 網頁切版</li>
                            <li>- RWD響應式設計</li>
                            <li>- SASS管理</li>
                            <li>- tailwind、bootstrap</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="mb-2 font-semibold">JavaScript</h3>
                        <ul class="text-[15px] space-y-2">
                            <li>- 基礎操作DOM、Event事件處理</li>
                            <li>- 使用ES6語法</li>
                            <li>- 與後端串接API(AXIOS/Fetch)</li>
                        </ul>
                    </li>
                    <li>
                        <h3 class="mb-2 font-semibold">Vue</h3>
                        <ul class="text-[15px] space-y-2">
                            <li>- 使用Vue3 Composition API</li>
                            <li>- Vue Cli 建置專案</li>
                            <li>- Vue Router、Vuex 進行路由與狀態管理</li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>
