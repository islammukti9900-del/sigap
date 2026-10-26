@extends('layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <p class="text-slate-500 text-sm">Kelola akun staff (admin & petugas) <span class="font-bold text-slate-700">({{ $users->total() }} akun)</span></p>
    <a href="{{ route('pengguna.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-cyan-600 hover:brightness-110 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-lg shadow-emerald-600/25 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        Tambah Akun Staff
    </a>
</div>

<div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-slate-400 border-b border-slate-100 bg-slate-50/50">
                    <th class="px-6 py-3.5 font-semibold">Nama</th>
                    <th class="px-6 py-3.5 font-semibold">Email</th>
                    <th class="px-6 py-3.5 font-semibold">Role</th>
                    <th class="px-6 py-3.5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-50">
                @forelse($users as $u)
                <tr class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $u->role === 'admin' ? 'from-cyan-400 to-cyan-600' : 'from-sky-400 to-sky-600' }} flex items-center justify-center text-white font-bold shrink-0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <p class="font-bold text-slate-800">{{ $u->name }}
                                @if($u->id === auth()->id())
                                <span class="text-xs font-semibold text-slate-400">(Anda)</span>
                                @endif
                            </p>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-slate-600">{{ $u->email }}</td>
                    <td class="px-6 py-3.5">
                        @if($u->role === 'admin')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800">Admin</span>
                        @elseif($u->role === 'petugas')
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-700">Petugas</span>
                        @else
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Pelapor</span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5">
                        <div class="flex justify-end gap-1.5">
                            <a href="{{ route('pengguna.edit', $u->id) }}" title="Ubah" class="p-2 rounded-lg text-amber-600 hover:bg-amber-50 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                            </a>
                            @if($u->id !== auth()->id())
                            <form action="{{ route('pengguna.destroy', $u->id) }}" method="POST" onsubmit="event.preventDefault(); askDelete(this, 'Hapus akun staff ini?')">
                                @csrf @method('DELETE')
                                <button title="Hapus" class="p-2 rounded-lg text-red-500 hover:bg-red-50 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-16 text-center text-slate-400">Belum ada akun staff.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $users->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
