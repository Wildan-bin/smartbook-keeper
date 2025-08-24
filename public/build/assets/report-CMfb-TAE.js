import{r as v,o as I,a as K,g as F,c as P,j as $,e as o,k as A,m as e,x as l,I as H,b as d,F as y,q as h,p as n,M as J,u as z,l as p,y as g,W as N}from"./app-BiIYKdZI.js";import{M as R}from"./MainLayout-lhzYw6Fn.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./createLucideIcon-BV5_2BVh.js";import"./x--wMcc203.js";import"./user-BwpOVRxr.js";const W=["value"],O={class:"flex items-center"},U={class:"flex-shrink-0"},q={class:"flex items-center"},G={class:"flex-shrink-0"},Y={class:"flex items-center"},Q={class:"flex-shrink-0"},X={class:"overflow-x-auto"},Z={class:"min-w-full divide-y divide-gray-200"},_={class:"bg-gray-50"},ee={class:"bg-white divide-y divide-gray-200"},te={key:0},ae=["colspan"],le={class:"overflow-x-auto"},se={class:"min-w-full divide-y divide-gray-200"},ne={class:"bg-gray-50"},re={class:"bg-white divide-y divide-gray-200"},oe={key:0},ie=["colspan"],de={id:"hidden-report",class:"hidden"},ue={class:"header"},me={class:"report-title"},xe={class:"report-period"},pe={class:"summary-section"},ce={class:"summary-grid"},ve={class:"summary-item"},ge={class:"summary-value income"},ye={class:"summary-item"},he={class:"summary-value expense"},fe={class:"summary-item"},be={key:0,class:"table-section"},we={class:"text-center"},ke={class:"amount-income"},Se={key:1,class:"page-break"},De={key:2,class:"table-section"},Te={class:"text-center"},Ce={class:"amount-expense"},Be={class:"footer"},je={__name:"report",props:{selectedMonth:{type:String,default:()=>new Date().toISOString().slice(0,7)},monthLabel:{type:String,default:""},periodStart:{type:String,default:""},periodEnd:{type:String,default:""},summary:{type:Object,default:()=>({totalIncome:0,totalExpense:0,netBalance:0,incomeChange:0,expenseChange:0})},incomeTransactions:{type:Array,default:()=>[]},expenseTransactions:{type:Array,default:()=>[]},availableMonths:{type:Array,default:()=>[]}},setup(i){const t=v(!1),r=v(!1),C=()=>{const u=window.innerWidth;t.value=u<640,r.value=u>=640&&u<1024};I(()=>{C(),window.addEventListener("resize",C)}),K(()=>{window.removeEventListener("resize",C)});const f=i,B=v(f.selectedMonth),b=v("date"),w=v("desc"),k=v("date"),S=v("desc"),L=F().props.auth.user,M=P(()=>[...f.incomeTransactions].sort((a,s)=>{const m=a[b.value],x=s[b.value];return w.value==="asc"?m>x?1:-1:m<x?1:-1})),V=P(()=>[...f.expenseTransactions].sort((a,s)=>{const m=a[k.value],x=s[k.value];return S.value==="asc"?m>x?1:-1:m<x?1:-1})),c=u=>u==null?"Rp 0":new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0,maximumFractionDigits:0}).format(u),D=u=>new Date(u).toLocaleDateString("id-ID",{day:"2-digit",month:"2-digit",year:"numeric"}),E=()=>{N.get(route("reports.index"),{month:B.value},{preserveState:!0,preserveScroll:!0})},T=(u,a)=>{a==="income"?b.value===u?w.value=w.value==="asc"?"desc":"asc":(b.value=u,w.value="desc"):k.value===u?S.value=S.value==="asc"?"desc":"asc":(k.value=u,S.value="desc")},j=()=>{try{const u=document.getElementById("hidden-report");if(!u){alert("Terjadi kesalahan: Element laporan tidak ditemukan");return}const a=window.open("","_blank","width=1000,height=1000"),s=u.innerHTML;a.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Keuangan ${f.monthLabel}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        line-height: 1.4;
                        color: #333;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 2px solid #333;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    .company-name {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .report-title {
                        font-size: 20px;
                        margin-bottom: 10px;
                    }
                    .report-period {
                        font-size: 14px;
                        color: #666;
                    }
                    .summary-section {
                        margin: 30px 0;
                        padding: 20px;
                        background-color: #f8f9fa;
                        border-radius: 8px;
                    }
                    .summary-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        text-align: center;
                    }
                    .summary-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr 1fr;
                        gap: 20px;
                        text-align: center;
                    }
                    .summary-item {
                        padding: 15px;
                        background: white;
                        border-radius: 6px;
                        border: 1px solid #ddd;
                    }
                    .summary-label {
                        font-size: 12px;
                        color: #666;
                        text-transform: uppercase;
                        margin-bottom: 5px;
                    }
                    .summary-value {
                        font-size: 16px;
                        font-weight: bold;
                    }
                    .summary-value.income { color: #22c55e; }
                    .summary-value.expense { color: #ef4444; }
                    .summary-value.balance.positive { color: #22c55e; }
                    .summary-value.balance.negative { color: #ef4444; }
                    
                    .table-section {
                        margin: 30px 0;
                        page-break-inside: avoid;
                    }
                    .table-title {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        color: #333;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                        font-size: 12px;
                    }
                    th {
                        background-color: #f8f9fa;
                        font-weight: bold;
                        text-align: center;
                    }
                    .amount-income {
                        color: #22c55e;
                        font-weight: bold;
                        text-align: right;
                    }
                    .amount-expense {
                        color: #ef4444;
                        font-weight: bold;
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .footer {
                        margin-top: 50px;
                        text-align: right;
                        font-size: 12px;
                        color: #666;
                    }
                    @media print {
                        body { margin: 0; }
                        .page-break { page-break-after: always; }
                    }
                </style>
            </head>
            <body>
                ${s}
            </body>
            </html>
        `),a.document.close(),a.focus(),setTimeout(()=>{a.print(),setTimeout(()=>{a.close()},100)},500)}catch(u){console.error("Error exporting PDF:",u),alert("Terjadi kesalahan saat mengekspor PDF. Silakan coba lagi.")}};return(u,a)=>(o(),$(R,{user:z(L)},{default:A(()=>[e("div",{class:l(["py-4 sm:py-6 space-y-4 sm:space-y-6",[t.value?"px-4":r.value?"px-6":"ps-16 pe-8"]])},[e("div",{class:l(["flex items-start justify-between gap-4",[t.value?"flex-col":"flex-row items-center"]])},[e("h1",{class:l(["font-medium tracking-tight text-gray-900",[t.value?"text-2xl":r.value?"text-3xl":"text-5xl"]])}," 📊 Laporan Keuangan ",2),e("div",{class:l(["flex gap-2 sm:gap-3",[t.value?"w-full flex-col":"flex-row"]])},[H(e("select",{"onUpdate:modelValue":a[0]||(a[0]=s=>B.value=s),onChange:E,class:l(["border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500",[t.value?"rounded-lg px-3 py-2 text-sm w-full":"rounded-md"]])},[(o(!0),d(y,null,h(i.availableMonths,s=>(o(),d("option",{key:s.value,value:s.value},n(s.label),9,W))),128))],34),[[J,B.value]]),e("button",{onClick:j,class:l(["flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white shadow-sm transition-colors",[t.value?"px-3 py-2 rounded-lg text-sm w-full":"px-4 py-2 rounded-md"]])},[a[5]||(a[5]=e("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"})],-1)),e("span",{class:l([t.value?"text-sm":""])},"Export PDF",2)],2)],2)],2),e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("div",{class:l(["",[t.value?"mb-3":"mb-4"]])},[e("h2",{class:l(["font-bold text-gray-800",[t.value?"text-lg mb-1":r.value?"text-xl mb-2":"text-2xl mb-2"]])}," Laporan Keuangan "+n(i.monthLabel),3),e("p",{class:l(["text-gray-600",[t.value?"text-sm":"text-base"]])}," Periode: "+n(i.periodStart)+" - "+n(i.periodEnd),3),e("p",{class:l(["text-gray-600",[t.value?"text-sm":"text-base"]])}," Pengguna: "+n(z(L).name),3)],2)],2),e("div",{class:l(["grid gap-4 sm:gap-6",[t.value?"grid-cols-1":"grid-cols-1 md:grid-cols-3"]])},[e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("div",O,[e("div",U,[e("div",{class:l(["bg-green-100 rounded-full flex items-center justify-center",[t.value?"w-6 h-6":"w-8 h-8"]])},[(o(),d("svg",{class:l(["text-green-600",[t.value?"w-4 h-4":"w-5 h-5"]]),fill:"currentColor",viewBox:"0 0 20 20"},a[6]||(a[6]=[e("path",{"fill-rule":"evenodd",d:"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z","clip-rule":"evenodd"},null,-1)]),2))],2)]),e("div",{class:l(["w-0 flex-1",[t.value?"ml-3":"ml-5"]])},[e("dt",{class:l(["font-medium text-gray-500 truncate",[t.value?"text-xs":"text-sm"]])}," Total Pemasukan ",2),e("dd",{class:l(["font-medium text-gray-900",[t.value?"text-sm":"text-lg"]])},n(c(i.summary.totalIncome)),3),i.summary.incomeChange!==0?(o(),d("dd",{key:0,class:l(["text-green-600",[t.value?"text-xs":"text-sm"]])},n(i.summary.incomeChange>0?"+":"")+n(i.summary.incomeChange)+"% dari bulan lalu ",3)):p("",!0)],2)])],2),e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("div",q,[e("div",G,[e("div",{class:l(["bg-red-100 rounded-full flex items-center justify-center",[t.value?"w-6 h-6":"w-8 h-8"]])},[(o(),d("svg",{class:l(["text-red-600",[t.value?"w-4 h-4":"w-5 h-5"]]),fill:"currentColor",viewBox:"0 0 20 20"},a[7]||(a[7]=[e("path",{"fill-rule":"evenodd",d:"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z","clip-rule":"evenodd"},null,-1)]),2))],2)]),e("div",{class:l(["w-0 flex-1",[t.value?"ml-3":"ml-5"]])},[e("dt",{class:l(["font-medium text-gray-500 truncate",[t.value?"text-xs":"text-sm"]])}," Total Pengeluaran ",2),e("dd",{class:l(["font-medium text-gray-900",[t.value?"text-sm":"text-lg"]])},n(c(i.summary.totalExpense)),3),i.summary.expenseChange!==0?(o(),d("dd",{key:0,class:l(["text-red-600",[t.value?"text-xs":"text-sm"]])},n(i.summary.expenseChange>0?"+":"")+n(i.summary.expenseChange)+"% dari bulan lalu ",3)):p("",!0)],2)])],2),e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("div",Y,[e("div",Q,[e("div",{class:l(["bg-blue-100 rounded-full flex items-center justify-center",[t.value?"w-6 h-6":"w-8 h-8"]])},[(o(),d("svg",{class:l(["text-blue-600",[t.value?"w-4 h-4":"w-5 h-5"]]),fill:"currentColor",viewBox:"0 0 20 20"},a[8]||(a[8]=[e("path",{"fill-rule":"evenodd",d:"M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z","clip-rule":"evenodd"},null,-1)]),2))],2)]),e("div",{class:l(["w-0 flex-1",[t.value?"ml-3":"ml-5"]])},[e("dt",{class:l(["font-medium text-gray-500 truncate",[t.value?"text-xs":"text-sm"]])}," Saldo Bersih ",2),e("dd",{class:l(["font-medium",[t.value?"text-sm":"text-lg",i.summary.netBalance>=0?"text-green-600":"text-red-600"]])},n(c(i.summary.netBalance)),3)],2)])],2)],2),e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("h3",{class:l(["font-medium text-gray-900",[t.value?"text-base mb-3":"text-lg mb-4"]])}," 📈 Daftar Pemasukan ",2),e("div",X,[e("table",Z,[e("thead",_,[e("tr",null,[e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]]),onClick:a[1]||(a[1]=s=>T("date","income"))},a[9]||(a[9]=[g(" Tanggal "),e("svg",{class:"w-3 h-3 sm:w-4 sm:h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)]),2),e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Kategori ",2),e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]]),onClick:a[2]||(a[2]=s=>T("amount","income"))},a[10]||(a[10]=[g(" Jumlah "),e("svg",{class:"w-3 h-3 sm:w-4 sm:h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)]),2),t.value?p("",!0):(o(),d("th",{key:0,class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Sumber Saldo ",2)),t.value?p("",!0):(o(),d("th",{key:1,class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Deskripsi ",2))])]),e("tbody",ee,[(o(!0),d(y,null,h(M.value,s=>{var m,x;return o(),d("tr",{key:s.id},[e("td",{class:l(["whitespace-nowrap text-gray-900",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(D(s.date)),3),e("td",{class:l(["whitespace-nowrap text-gray-900",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(((m=s.category)==null?void 0:m.name)||"-"),3),e("td",{class:l(["whitespace-nowrap font-medium text-green-600",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(c(s.amount)),3),t.value?p("",!0):(o(),d("td",{key:0,class:l(["whitespace-nowrap text-gray-900",[r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(((x=s.balance)==null?void 0:x.name)||"-"),3)),t.value?p("",!0):(o(),d("td",{key:1,class:l(["text-gray-900",[r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(s.description||"-"),3))])}),128)),i.incomeTransactions.length===0?(o(),d("tr",te,[e("td",{class:l(["text-center text-gray-500",[t.value?"px-3 py-4 text-xs":"px-6 py-4 text-sm"]]),colspan:t.value?3:5}," Tidak ada data pemasukan ",10,ae)])):p("",!0)])])])],2),e("div",{class:l(["bg-white shadow-md border border-gray-100",[t.value?"rounded-xl p-4":r.value?"rounded-2xl p-5":"rounded-3xl p-6"]])},[e("h3",{class:l(["font-medium text-gray-900",[t.value?"text-base mb-3":"text-lg mb-4"]])}," 📉 Daftar Pengeluaran ",2),e("div",le,[e("table",se,[e("thead",ne,[e("tr",null,[e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]]),onClick:a[3]||(a[3]=s=>T("date","expense"))},a[11]||(a[11]=[g(" Tanggal "),e("svg",{class:"w-3 h-3 sm:w-4 sm:h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)]),2),e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Kategori ",2),e("th",{class:l(["text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]]),onClick:a[4]||(a[4]=s=>T("amount","expense"))},a[12]||(a[12]=[g(" Jumlah "),e("svg",{class:"w-3 h-3 sm:w-4 sm:h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)]),2),t.value?p("",!0):(o(),d("th",{key:0,class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Sumber Saldo ",2)),t.value?p("",!0):(o(),d("th",{key:1,class:l(["text-left font-medium text-gray-500 uppercase tracking-wider",[r.value?"px-4 py-3 text-xs":"px-6 py-3 text-xs"]])}," Deskripsi ",2))])]),e("tbody",re,[(o(!0),d(y,null,h(V.value,s=>{var m,x;return o(),d("tr",{key:s.id},[e("td",{class:l(["whitespace-nowrap text-gray-900",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(D(s.date)),3),e("td",{class:l(["whitespace-nowrap text-gray-900",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(((m=s.category)==null?void 0:m.name)||"-"),3),e("td",{class:l(["whitespace-nowrap font-medium text-red-600",[t.value?"px-3 py-2 text-xs":r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(c(s.amount)),3),t.value?p("",!0):(o(),d("td",{key:0,class:l(["whitespace-nowrap text-gray-900",[r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(((x=s.balance)==null?void 0:x.name)||"-"),3)),t.value?p("",!0):(o(),d("td",{key:1,class:l(["text-gray-900",[r.value?"px-4 py-3 text-sm":"px-6 py-4 text-sm"]])},n(s.description||"-"),3))])}),128)),i.expenseTransactions.length===0?(o(),d("tr",oe,[e("td",{class:l(["text-center text-gray-500",[t.value?"px-3 py-4 text-xs":"px-6 py-4 text-sm"]]),colspan:t.value?3:5}," Tidak ada data pengeluaran ",10,ie)])):p("",!0)])])])],2)],2),e("div",de,[e("div",ue,[a[15]||(a[15]=e("div",{class:"company-name"},"SmartBook Keeper",-1)),e("div",me,"Laporan Keuangan "+n(i.monthLabel),1),e("div",xe,[g(" Periode: "+n(i.periodStart)+" - "+n(i.periodEnd),1),a[13]||(a[13]=e("br",null,null,-1)),g(" Pengguna: "+n(z(L).name),1),a[14]||(a[14]=e("br",null,null,-1)),g(" Dicetak pada: "+n(new Date().toLocaleDateString("id-ID")),1)])]),e("div",pe,[a[19]||(a[19]=e("div",{class:"summary-title"},"Ringkasan Keuangan",-1)),e("div",ce,[e("div",ve,[a[16]||(a[16]=e("div",{class:"summary-label"},"Total Pemasukan",-1)),e("div",ge,n(c(i.summary.totalIncome)),1)]),e("div",ye,[a[17]||(a[17]=e("div",{class:"summary-label"},"Total Pengeluaran",-1)),e("div",he,n(c(i.summary.totalExpense)),1)]),e("div",fe,[a[18]||(a[18]=e("div",{class:"summary-label"},"Saldo Bersih",-1)),e("div",{class:l(["summary-value balance",i.summary.netBalance>=0?"positive":"negative"])},n(c(i.summary.netBalance)),3)])])]),i.incomeTransactions.length>0?(o(),d("div",be,[a[21]||(a[21]=e("div",{class:"table-title"},"Daftar Pemasukan",-1)),e("table",null,[a[20]||(a[20]=e("thead",null,[e("tr",null,[e("th",null,"Tanggal"),e("th",null,"Kategori"),e("th",null,"Jumlah"),e("th",null,"Sumber Saldo"),e("th",null,"Deskripsi")])],-1)),e("tbody",null,[(o(!0),d(y,null,h(M.value,s=>{var m,x;return o(),d("tr",{key:s.id},[e("td",we,n(D(s.date)),1),e("td",null,n(((m=s.category)==null?void 0:m.name)||"-"),1),e("td",ke,n(c(s.amount)),1),e("td",null,n(((x=s.balance)==null?void 0:x.name)||"-"),1),e("td",null,n(s.description||"-"),1)])}),128))])])])):p("",!0),i.incomeTransactions.length>0&&i.expenseTransactions.length>0?(o(),d("div",Se)):p("",!0),i.expenseTransactions.length>0?(o(),d("div",De,[a[23]||(a[23]=e("div",{class:"table-title"},"Daftar Pengeluaran",-1)),e("table",null,[a[22]||(a[22]=e("thead",null,[e("tr",null,[e("th",null,"Tanggal"),e("th",null,"Kategori"),e("th",null,"Jumlah"),e("th",null,"Sumber Saldo"),e("th",null,"Deskripsi")])],-1)),e("tbody",null,[(o(!0),d(y,null,h(V.value,s=>{var m,x;return o(),d("tr",{key:s.id},[e("td",Te,n(D(s.date)),1),e("td",null,n(((m=s.category)==null?void 0:m.name)||"-"),1),e("td",Ce,n(c(s.amount)),1),e("td",null,n(((x=s.balance)==null?void 0:x.name)||"-"),1),e("td",null,n(s.description||"-"),1)])}),128))])])])):p("",!0),e("div",Be,[a[24]||(a[24]=e("div",null,"SmartBook Keeper - Sistem Pengelolaan Keuangan",-1)),e("div",null,"Generated on "+n(new Date().toLocaleString("id-ID")),1)])])]),_:1},8,["user"]))}};export{je as default};
