<div class="relative" id="notifWrapper">
    {{-- Bell Icon Button --}}
    <button id="btnNotif" onclick="toggleNotifDropdown()"
        class="relative text-gray-500 hover:text-[#C97B2E] transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        {{-- Badge unread --}}
        <span id="notifBadge"
            class="hidden absolute -top-1.5 -right-1.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center leading-none">
            0
        </span>
    </button>

    {{-- Dropdown Notifikasi --}}
    <div id="notifDropdown"
        class="hidden absolute right-0 top-10 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">

        {{-- Header Dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <h4 class="font-bold text-gray-800 text-sm">Notifikasi</h4>
            <button onclick="bacaSemua()" class="text-xs text-[#C97B2E] font-semibold hover:underline">
                Tandai semua dibaca
            </button>
        </div>

        {{-- List Notifikasi --}}
        <div id="notifList" class="overflow-y-auto" style="max-height: 360px;">
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm">Belum ada notifikasi</p>
            </div>
        </div>
    </div>
</div>

<script>
const notifColors = {
    orange: { bg: 'bg-orange-100', text: 'text-orange-600' },
    green:  { bg: 'bg-green-100',  text: 'text-green-600'  },
    purple: { bg: 'bg-purple-100', text: 'text-purple-600' },
    red:    { bg: 'bg-red-100',    text: 'text-red-600'    },
};

const notifIcons = {
    bell:  `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>`,
    check: `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
    fire:  `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>`,
};

let notifTerbuka = false;

function toggleNotifDropdown() {
    notifTerbuka = !notifTerbuka;
    document.getElementById('notifDropdown').classList.toggle('hidden', !notifTerbuka);
}

// Tutup dropdown jika klik di luar
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifWrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        notifTerbuka = false;
        document.getElementById('notifDropdown').classList.add('hidden');
    }
});

function renderNotif(notifications) {
    const list = document.getElementById('notifList');
    if (!notifications.length) {
        list.innerHTML = `
        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
            <svg class="w-10 h-10 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-sm">Belum ada notifikasi</p>
        </div>`;
        return;
    }

    list.innerHTML = notifications.map(n => {
        const color = notifColors[n.warna] || notifColors.orange;
        const icon  = notifIcons[n.ikon]  || notifIcons.bell;
        const unreadClass = n.is_read ? '' : 'bg-orange-50';
        return `
        <div class="flex gap-3 px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition cursor-pointer ${unreadClass}"
            onclick="bacaNotif(${n.id}, this)">
            <div class="w-9 h-9 rounded-xl ${color.bg} ${color.text} flex items-center justify-center flex-shrink-0 mt-0.5">
                ${icon}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-800 ${n.is_read ? '' : 'font-bold'}">${n.judul}</p>
                <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">${n.pesan}</p>
                <p class="text-[10px] text-gray-400 mt-1">${n.waktu}</p>
            </div>
            ${!n.is_read ? '<div class="w-2 h-2 bg-[#C97B2E] rounded-full mt-2 flex-shrink-0"></div>' : ''}
        </div>`;
    }).join('');
}

function bacaNotif(id, el) {
    fetch(`/notifikasi/${id}/baca`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(() => {
        el.classList.remove('bg-orange-50');
        el.querySelector('.bg-\\[\\#C97B2E\\]')?.remove();
        fetchNotif();
    });
}

function bacaSemua() {
    fetch('/notifikasi/baca-semua', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    }).then(() => fetchNotif());
}

function fetchNotif() {
    fetch('/notifikasi/fetch', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        // Update badge
        const badge = document.getElementById('notifBadge');
        if (data.unread > 0) {
            badge.textContent = data.unread > 9 ? '9+' : data.unread;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
        renderNotif(data.notifications);
    })
    .catch(() => {});
}

// Polling setiap 8 detik
fetchNotif();
setInterval(fetchNotif, 8000);
</script>