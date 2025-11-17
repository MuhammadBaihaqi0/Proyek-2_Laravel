<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Foto Profil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        Upload Foto Profil
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Ganti foto profil Anda di sini.
                    </p>
                </header>

                <div class="mt-6 flex flex-col items-center">
                    <img class="h-32 w-32 rounded-full object-cover border-4 border-indigo-500 mb-4" 
                         src="{{ $avatar_path }}" 
                         alt="{{ $user->username }}">
                    <p class="text-sm text-gray-600">Foto profil saat ini.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="avatar" class="block font-medium text-sm text-gray-700">Pilih Foto Baru</label>
                        <input id="avatar" name="avatar" type="file" class="mt-1 block w-full text-sm text-gray-500
                               file:mr-4 file:py-2 file:px-4
                               file:rounded-md file:border-0
                               file:text-sm file:font-semibold
                               file:bg-indigo-50 file:text-indigo-700
                               hover:file:bg-indigo-100" />
                        @error('avatar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Simpan Foto
                        </button>

                        @if (session('status') === 'profile-updated')
                            <p class="text-sm text-gray-600">Foto profil berhasil diupdate.</p>
                        @endif
                    </div>
                </form>

                <div class="border-t border-gray-200 pt-6 mt-6">
                    <form method="post" action="{{ route('profile.destroy_avatar') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('delete')
                        <h2 class="text-lg font-medium text-gray-900">
                            Hapus Foto Profil
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Klik tombol di bawah untuk menghapus foto profil Anda dan kembali ke inisial nama.
                        </p>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Hapus Foto Profil
                        </button>
                        @if (session('status') === 'avatar-deleted')
                            <p class="text-sm text-gray-600">Foto profil berhasil dihapus.</p>
                        @endif
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>