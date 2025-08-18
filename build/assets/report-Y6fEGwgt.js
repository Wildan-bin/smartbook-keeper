import{r as x,g as j,c as L,j as E,e as l,k as I,m as e,I as K,b as d,F as p,q as g,p as a,M as F,y as u,u as C,l as c,x as M,W as H}from"./app-BGxFCu_E.js";import{M as $}from"./MainLayout-RzF0llV9.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./createLucideIcon-BHjj39h4.js";import"./x-D4z6AD46.js";import"./user-DCjMRFep.js";const R={class:"py-6 space-y-6 ps-16 pe-8"},A={class:"flex justify-between items-center"},J={class:"flex gap-3"},N=["value"],O={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},W={class:"mb-4"},_={class:"text-2xl font-bold text-gray-800"},q={class:"text-gray-600"},G={class:"text-gray-600"},U={class:"grid grid-cols-1 md:grid-cols-3 gap-6"},Y={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},Q={class:"flex items-center"},X={class:"ml-5 w-0 flex-1"},Z={class:"text-lg font-medium text-gray-900"},ee={key:0,class:"text-sm text-green-600"},te={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},se={class:"flex items-center"},ae={class:"ml-5 w-0 flex-1"},ne={class:"text-lg font-medium text-gray-900"},oe={key:0,class:"text-sm text-red-600"},le={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},re={class:"flex items-center"},ie={class:"ml-5 w-0 flex-1"},de={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},me={class:"overflow-x-auto"},ue={class:"min-w-full divide-y divide-gray-200"},ce={class:"bg-gray-50"},xe={class:"bg-white divide-y divide-gray-200"},pe={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},ge={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},ye={class:"px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600"},he={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},ve={class:"px-6 py-4 text-sm text-gray-900"},fe={key:0},be={class:"rounded-3xl bg-white p-6 shadow-md inset-shadow-sm"},we={class:"overflow-x-auto"},ke={class:"min-w-full divide-y divide-gray-200"},Se={class:"bg-gray-50"},De={class:"bg-white divide-y divide-gray-200"},Ce={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},Te={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},Be={class:"px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600"},ze={class:"px-6 py-4 whitespace-nowrap text-sm text-gray-900"},Le={class:"px-6 py-4 text-sm text-gray-900"},Me={key:0},Ve={id:"hidden-report",class:"hidden"},Pe={class:"header"},je={class:"report-title"},Ee={class:"report-period"},Ie={class:"summary-section"},Ke={class:"summary-grid"},Fe={class:"summary-item"},He={class:"summary-value income"},$e={class:"summary-item"},Re={class:"summary-value expense"},Ae={class:"summary-item"},Je={key:0,class:"table-section"},Ne={class:"text-center"},Oe={class:"amount-income"},We={key:1,class:"page-break"},_e={key:2,class:"table-section"},qe={class:"text-center"},Ge={class:"amount-expense"},Ue={class:"footer"},st={__name:"report",props:{selectedMonth:{type:String,default:()=>new Date().toISOString().slice(0,7)},monthLabel:{type:String,default:""},periodStart:{type:String,default:""},periodEnd:{type:String,default:""},summary:{type:Object,default:()=>({totalIncome:0,totalExpense:0,netBalance:0,incomeChange:0,expenseChange:0})},incomeTransactions:{type:Array,default:()=>[]},expenseTransactions:{type:Array,default:()=>[]},availableMonths:{type:Array,default:()=>[]}},setup(n){const y=n,S=x(y.selectedMonth),h=x("date"),v=x("desc"),f=x("date"),b=x("desc"),D=j().props.auth.user,T=L(()=>[...y.incomeTransactions].sort((t,s)=>{const r=t[h.value],i=s[h.value];return v.value==="asc"?r>i?1:-1:r<i?1:-1})),B=L(()=>[...y.expenseTransactions].sort((t,s)=>{const r=t[f.value],i=s[f.value];return b.value==="asc"?r>i?1:-1:r<i?1:-1})),m=o=>o==null?"Rp 0":new Intl.NumberFormat("id-ID",{style:"currency",currency:"IDR",minimumFractionDigits:0,maximumFractionDigits:0}).format(o),w=o=>new Date(o).toLocaleDateString("id-ID",{day:"2-digit",month:"2-digit",year:"numeric"}),V=()=>{H.get(route("reports.index"),{month:S.value},{preserveState:!0,preserveScroll:!0})},k=(o,t)=>{t==="income"?h.value===o?v.value=v.value==="asc"?"desc":"asc":(h.value=o,v.value="desc"):f.value===o?b.value=b.value==="asc"?"desc":"asc":(f.value=o,b.value="desc")},z=()=>{try{const o=document.getElementById("hidden-report");if(!o){alert("Terjadi kesalahan: Element laporan tidak ditemukan");return}const t=window.open("","_blank","width=1000,height=1000"),s=o.innerHTML;t.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Keuangan ${y.monthLabel}</title>
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
        `),t.document.close(),t.focus(),setTimeout(()=>{t.print(),setTimeout(()=>{t.close()},100)},500)}catch(o){console.error("Error exporting PDF:",o),alert("Terjadi kesalahan saat mengekspor PDF. Silakan coba lagi.")}},P=()=>{z()};return(o,t)=>(l(),E($,{user:C(D)},{default:I(()=>[e("div",R,[e("div",A,[t[7]||(t[7]=e("h1",{class:"text-5xl font-medium tracking-tight"}," Laporan Keuangan ",-1)),e("div",J,[K(e("select",{"onUpdate:modelValue":t[0]||(t[0]=s=>S.value=s),onChange:V,class:"rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"},[(l(!0),d(p,null,g(n.availableMonths,s=>(l(),d("option",{key:s.value,value:s.value},a(s.label),9,N))),128))],544),[[F,S.value]]),e("button",{onClick:z,class:"flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm"},t[5]||(t[5]=[e("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"})],-1),u(" Export PDF ")])),e("button",{onClick:P,class:"flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm"},t[6]||(t[6]=[e("svg",{class:"w-4 h-4",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"})],-1),u(" Cetak ")]))])]),e("div",O,[e("div",W,[e("h2",_,"Laporan Keuangan "+a(n.monthLabel),1),e("p",q,"Periode: "+a(n.periodStart)+" - "+a(n.periodEnd),1),e("p",G,"Pengguna: "+a(C(D).name),1)])]),e("div",U,[e("div",Y,[e("div",Q,[t[9]||(t[9]=e("div",{class:"flex-shrink-0"},[e("div",{class:"w-8 h-8 bg-green-100 rounded-full flex items-center justify-center"},[e("svg",{class:"w-5 h-5 text-green-600",fill:"currentColor",viewBox:"0 0 20 20"},[e("path",{"fill-rule":"evenodd",d:"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z","clip-rule":"evenodd"})])])],-1)),e("div",X,[t[8]||(t[8]=e("dt",{class:"text-sm font-medium text-gray-500 truncate"},"Total Pemasukan",-1)),e("dd",Z,a(m(n.summary.totalIncome)),1),n.summary.incomeChange!==0?(l(),d("dd",ee,a(n.summary.incomeChange>0?"+":"")+a(n.summary.incomeChange)+"% dari bulan lalu ",1)):c("",!0)])])]),e("div",te,[e("div",se,[t[11]||(t[11]=e("div",{class:"flex-shrink-0"},[e("div",{class:"w-8 h-8 bg-red-100 rounded-full flex items-center justify-center"},[e("svg",{class:"w-5 h-5 text-red-600",fill:"currentColor",viewBox:"0 0 20 20"},[e("path",{"fill-rule":"evenodd",d:"M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z","clip-rule":"evenodd"})])])],-1)),e("div",ae,[t[10]||(t[10]=e("dt",{class:"text-sm font-medium text-gray-500 truncate"},"Total Pengeluaran",-1)),e("dd",ne,a(m(n.summary.totalExpense)),1),n.summary.expenseChange!==0?(l(),d("dd",oe,a(n.summary.expenseChange>0?"+":"")+a(n.summary.expenseChange)+"% dari bulan lalu ",1)):c("",!0)])])]),e("div",le,[e("div",re,[t[13]||(t[13]=e("div",{class:"flex-shrink-0"},[e("div",{class:"w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"},[e("svg",{class:"w-5 h-5 text-blue-600",fill:"currentColor",viewBox:"0 0 20 20"},[e("path",{"fill-rule":"evenodd",d:"M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z","clip-rule":"evenodd"})])])],-1)),e("div",ie,[t[12]||(t[12]=e("dt",{class:"text-sm font-medium text-gray-500 truncate"},"Saldo Bersih",-1)),e("dd",{class:M(["text-lg font-medium",n.summary.netBalance>=0?"text-green-600":"text-red-600"])},a(m(n.summary.netBalance)),3)])])])]),e("div",de,[t[20]||(t[20]=e("h3",{class:"text-lg font-medium text-gray-900 mb-4"},"Daftar Pemasukan",-1)),e("div",me,[e("table",ue,[e("thead",ce,[e("tr",null,[e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer",onClick:t[1]||(t[1]=s=>k("date","income"))},t[14]||(t[14]=[u(" Tanggal "),e("svg",{class:"w-4 h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)])),t[16]||(t[16]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Kategori",-1)),e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer",onClick:t[2]||(t[2]=s=>k("amount","income"))},t[15]||(t[15]=[u(" Jumlah "),e("svg",{class:"w-4 h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)])),t[17]||(t[17]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Sumber Saldo",-1)),t[18]||(t[18]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Deskripsi",-1))])]),e("tbody",xe,[(l(!0),d(p,null,g(T.value,s=>{var r,i;return l(),d("tr",{key:s.id},[e("td",pe,a(w(s.date)),1),e("td",ge,a(((r=s.category)==null?void 0:r.name)||"-"),1),e("td",ye,a(m(s.amount)),1),e("td",he,a(((i=s.balance)==null?void 0:i.name)||"-"),1),e("td",ve,a(s.description||"-"),1)])}),128)),n.incomeTransactions.length===0?(l(),d("tr",fe,t[19]||(t[19]=[e("td",{colspan:"5",class:"px-6 py-4 text-center text-gray-500"},"Tidak ada data pemasukan",-1)]))):c("",!0)])])])]),e("div",be,[t[27]||(t[27]=e("h3",{class:"text-lg font-medium text-gray-900 mb-4"},"Daftar Pengeluaran",-1)),e("div",we,[e("table",ke,[e("thead",Se,[e("tr",null,[e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer",onClick:t[3]||(t[3]=s=>k("date","expense"))},t[21]||(t[21]=[u(" Tanggal "),e("svg",{class:"w-4 h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)])),t[23]||(t[23]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Kategori",-1)),e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer",onClick:t[4]||(t[4]=s=>k("amount","expense"))},t[22]||(t[22]=[u(" Jumlah "),e("svg",{class:"w-4 h-4 inline ml-1",fill:"none",stroke:"currentColor",viewBox:"0 0 24 24"},[e("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"})],-1)])),t[24]||(t[24]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Sumber Saldo",-1)),t[25]||(t[25]=e("th",{class:"px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"},"Deskripsi",-1))])]),e("tbody",De,[(l(!0),d(p,null,g(B.value,s=>{var r,i;return l(),d("tr",{key:s.id},[e("td",Ce,a(w(s.date)),1),e("td",Te,a(((r=s.category)==null?void 0:r.name)||"-"),1),e("td",Be,a(m(s.amount)),1),e("td",ze,a(((i=s.balance)==null?void 0:i.name)||"-"),1),e("td",Le,a(s.description||"-"),1)])}),128)),n.expenseTransactions.length===0?(l(),d("tr",Me,t[26]||(t[26]=[e("td",{colspan:"5",class:"px-6 py-4 text-center text-gray-500"},"Tidak ada data pengeluaran",-1)]))):c("",!0)])])])])]),e("div",Ve,[e("div",Pe,[t[30]||(t[30]=e("div",{class:"company-name"},"SmartBook Keeper",-1)),e("div",je,"Laporan Keuangan "+a(n.monthLabel),1),e("div",Ee,[u(" Periode: "+a(n.periodStart)+" - "+a(n.periodEnd),1),t[28]||(t[28]=e("br",null,null,-1)),u(" Pengguna: "+a(C(D).name),1),t[29]||(t[29]=e("br",null,null,-1)),u(" Dicetak pada: "+a(new Date().toLocaleDateString("id-ID")),1)])]),e("div",Ie,[t[34]||(t[34]=e("div",{class:"summary-title"},"Ringkasan Keuangan",-1)),e("div",Ke,[e("div",Fe,[t[31]||(t[31]=e("div",{class:"summary-label"},"Total Pemasukan",-1)),e("div",He,a(m(n.summary.totalIncome)),1)]),e("div",$e,[t[32]||(t[32]=e("div",{class:"summary-label"},"Total Pengeluaran",-1)),e("div",Re,a(m(n.summary.totalExpense)),1)]),e("div",Ae,[t[33]||(t[33]=e("div",{class:"summary-label"},"Saldo Bersih",-1)),e("div",{class:M(["summary-value balance",n.summary.netBalance>=0?"positive":"negative"])},a(m(n.summary.netBalance)),3)])])]),n.incomeTransactions.length>0?(l(),d("div",Je,[t[36]||(t[36]=e("div",{class:"table-title"},"Daftar Pemasukan",-1)),e("table",null,[t[35]||(t[35]=e("thead",null,[e("tr",null,[e("th",null,"Tanggal"),e("th",null,"Kategori"),e("th",null,"Jumlah"),e("th",null,"Sumber Saldo"),e("th",null,"Deskripsi")])],-1)),e("tbody",null,[(l(!0),d(p,null,g(T.value,s=>{var r,i;return l(),d("tr",{key:s.id},[e("td",Ne,a(w(s.date)),1),e("td",null,a(((r=s.category)==null?void 0:r.name)||"-"),1),e("td",Oe,a(m(s.amount)),1),e("td",null,a(((i=s.balance)==null?void 0:i.name)||"-"),1),e("td",null,a(s.description||"-"),1)])}),128))])])])):c("",!0),n.incomeTransactions.length>0&&n.expenseTransactions.length>0?(l(),d("div",We)):c("",!0),n.expenseTransactions.length>0?(l(),d("div",_e,[t[38]||(t[38]=e("div",{class:"table-title"},"Daftar Pengeluaran",-1)),e("table",null,[t[37]||(t[37]=e("thead",null,[e("tr",null,[e("th",null,"Tanggal"),e("th",null,"Kategori"),e("th",null,"Jumlah"),e("th",null,"Sumber Saldo"),e("th",null,"Deskripsi")])],-1)),e("tbody",null,[(l(!0),d(p,null,g(B.value,s=>{var r,i;return l(),d("tr",{key:s.id},[e("td",qe,a(w(s.date)),1),e("td",null,a(((r=s.category)==null?void 0:r.name)||"-"),1),e("td",Ge,a(m(s.amount)),1),e("td",null,a(((i=s.balance)==null?void 0:i.name)||"-"),1),e("td",null,a(s.description||"-"),1)])}),128))])])])):c("",!0),e("div",Ue,[t[39]||(t[39]=e("div",null,"SmartBook Keeper - Sistem Pengelolaan Keuangan",-1)),e("div",null,"Generated on "+a(new Date().toLocaleString("id-ID")),1)])])]),_:1},8,["user"]))}};export{st as default};
