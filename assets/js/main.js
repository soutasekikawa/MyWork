// 最小限の UI スクリプト（将来的に拡張）
console.log('SecuLearn loaded');
// assets/js/main.js 追記
document.addEventListener('DOMContentLoaded', function () {
    // ページ内リンクをスムーズスクロール（UIの高級感）
    document.querySelectorAll('a[href^="#"]').forEach(a=>{
      a.addEventListener('click', function(e){
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          e.preventDefault();
          target.scrollIntoView({behavior:'smooth'});
        }
      });
    });
  
    // ちょっとした動的背景（任意）
    const body = document.body;
    let hue = 200;
    setInterval(()=> {
      hue = (hue + 0.2) % 360;
      body.style.background = `linear-gradient(180deg, hsl(${hue} 30% 6% / 0.95), hsl(${(hue+30)%360} 30% 10% / 0.98))`;
    }, 4000);
  });
  