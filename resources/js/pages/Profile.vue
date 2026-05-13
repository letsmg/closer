<template>
  <AppLayout>
    <!-- Card do perfil estilo Tinder -->
    <div class="px-4 pt-4">
      <!-- Foto principal e dados básicos -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-4">
        <!-- Foto grande -->
        <div class="relative h-72 bg-gradient-to-br from-pink-400 to-purple-500 flex items-end justify-center">
          <img
            v-if="profile?.photos?.[0]?.full_url"
            :src="profile.photos[0].full_url"
            :alt="profile.nickname"
            class="w-full h-full object-cover"
          />
          <div v-else class="flex items-center justify-center h-full">
            <span class="text-6xl text-white/60">
              {{ profile?.nickname?.[0]?.toUpperCase() || '?' }}
            </span>
          </div>

          <!-- Badge de nível -->
          <div class="absolute top-4 right-4">
            <span
              class="px-3 py-1 rounded-full text-xs font-semibold text-white shadow-lg"
              :class="nivelBadgeClass"
            >
              {{ nivelLabel }}
            </span>
          </div>
        </div>

        <!-- Info -->
        <div class="p-4">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-bold text-gray-900">
                {{ profile?.nickname || authStore.user?.name }},
                <span class="font-normal">{{ profile?.age || '?' }}</span>
              </h2>
              <p class="text-sm text-gray-500 mt-0.5">
                {{ profile?.city?.name }}, {{ profile?.city?.state?.name }}
              </p>
            </div>
            <button
              @click="editandoFotos = true"
              class="p-2 text-pink-500 hover:bg-pink-50 rounded-full transition-colors"
              title="Editar fotos"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </button>
          </div>

          <!-- Mini bio -->
          <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ profile?.biography || 'Sem biografia ainda.' }}</p>

          <!-- Tags rápidas -->
          <div class="flex flex-wrap gap-2 mt-3">
            <span v-if="profile?.gender" class="px-2.5 py-1 bg-pink-50 text-pink-600 rounded-full text-xs font-medium">
              {{ genderLabel(profile.gender) }}
            </span>
            <span v-if="profile?.sexual_orientation" class="px-2.5 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-medium">
              {{ orientationLabel(profile.sexual_orientation) }}
            </span>
            <span v-if="profile?.purpose" class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-medium">
              {{ purposeLabel(profile.purpose) }}
            </span>
          </div>
        </div>
      </div>

      <!-- Miniaturas das outras fotos -->
      <div v-if="profile?.photos?.length > 1" class="flex gap-2 mb-4 overflow-x-auto pb-2">
        <img
          v-for="(photo, idx) in profile.photos.slice(0, 5)"
          :key="photo.id"
          :src="photo.full_url"
          :alt="`Foto ${idx + 1}`"
          class="w-16 h-16 rounded-xl object-cover flex-shrink-0 border-2"
          :class="idx === 0 ? 'border-pink-400' : 'border-transparent'"
          @click="photoIndex = idx"
        />
      </div>
    </div>

    <!-- ================================ -->
    <!-- SUBMENUS DE EDIÇÃO -->
    <!-- ================================ -->
    <div class="px-4 space-y-3 pb-6">
      <!-- 1. Preferências / Interesses -->
      <button
        @click="abaAtiva = 'hobbies'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Hobbies & Interesses</p>
            <p class="text-xs text-gray-500">{{ profile?.hobbies?.length || 0 }} hobbies cadastrados</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 2. Sexualidade e Gênero -->
      <button
        @click="abaAtiva = 'sexualidade'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Sexualidade & Gênero</p>
            <p class="text-xs text-gray-500">{{ genderLabel(profile?.gender) }} · {{ orientationLabel(profile?.sexual_orientation) }}</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 3. Localização e Raio -->
      <button
        @click="abaAtiva = 'localizacao'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Localização</p>
            <p class="text-xs text-gray-500">{{ profile?.city?.name }} · Raio: {{ preferencias?.search_radius_km || '?' }}km</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 4. Bloqueios -->
      <button
        @click="abaAtiva = 'bloqueios'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Bloqueios</p>
            <p class="text-xs text-gray-500">Regiões e usuários bloqueados</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 5. Plano e Upgrade -->
      <button
        @click="abaAtiva = 'upgrade'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Plano & Upgrade</p>
            <p class="text-xs text-gray-500">
              <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold text-white mr-1" :class="nivelBadgeClass">{{ nivelLabel }}</span>
              · Veja vantagens de Plus, Premium e mais
            </p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 6. Preferências de busca -->
      <button
        @click="abaAtiva = 'preferencias'"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Preferências de Busca</p>
            <p class="text-xs text-gray-500">Idade, gênero, orientação nos matches</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </button>

      <!-- 7. Segurança (2FA/Senha) -->
      <RouterLink
        to="/2fa"
        class="w-full bg-white rounded-xl p-4 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow"
      >
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
          </div>
          <div class="text-left">
            <p class="text-sm font-semibold text-gray-900">Segurança</p>
            <p class="text-xs text-gray-500">2FA, alterar senha</p>
          </div>
        </div>
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
      </RouterLink>
    </div>

    <!-- ================================ -->
    <!-- MODAIS DE EDIÇÃO -->
    <!-- ================================ -->

    <!-- Modal Hobbies -->
    <TransitionRoot appear :show="abaAtiva === 'hobbies'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Hobbies & Interesses</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">Selecione seus hobbies para encontrar pessoas com interesses em comum.</p>

                <div v-if="carregandoHobbies" class="flex justify-center py-8">
                  <div class="w-8 h-8 border-4 border-pink-500 border-t-transparent rounded-full animate-spin" />
                </div>

                <div v-else class="flex flex-wrap gap-2 max-h-80 overflow-y-auto">
                  <button
                    v-for="hobby in todosHobbies"
                    :key="hobby.id"
                    @click="toggleHobby(hobby.id)"
                    class="px-4 py-2 rounded-full text-sm font-medium transition-all border"
                    :class="hobbySelecionado(hobby.id)
                      ? 'bg-pink-500 text-white border-pink-500 shadow-md'
                      : 'bg-white text-gray-700 border-gray-200 hover:border-pink-300'"
                  >
                    {{ hobby.nome || hobby.name }}
                  </button>
                </div>

                <div class="mt-6">
                  <button
                    @click="salvarHobbies"
                    :disabled="salvando"
                    class="w-full py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl disabled:opacity-50 transition-all"
                  >
                    {{ salvando ? 'Salvando...' : 'Salvar Hobbies' }}
                  </button>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal Sexualidade -->
    <TransitionRoot appear :show="abaAtiva === 'sexualidade'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Sexualidade & Gênero</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <div class="space-y-5">
                  <!-- Gênero -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gênero</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="g in genderOptions"
                        :key="g.value"
                        @click="editProfile.gender = g.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editProfile.gender === g.value ? 'bg-pink-500 text-white border-pink-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ g.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Identidade de Gênero -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Identidade de Gênero</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="g in genderIdentityOptions"
                        :key="g.value"
                        @click="editProfile.gender_identity = g.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editProfile.gender_identity === g.value ? 'bg-purple-500 text-white border-purple-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ g.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Orientação Sexual -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orientação Sexual</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="o in orientationOptions"
                        :key="o.value"
                        @click="editProfile.sexual_orientation = o.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editProfile.sexual_orientation === o.value ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ o.label }}
                      </button>
                    </div>
                  </div>
                </div>

                <button
                  @click="salvarSexualidade"
                  class="w-full mt-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
                >
                  Salvar
                </button>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal Localização -->
    <TransitionRoot appear :show="abaAtiva === 'localizacao'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Localização</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <div class="space-y-5">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Raio de busca (km)</label>
                    <input
                      type="range"
                      min="1"
                      max="200"
                      v-model.number="editPrefs.search_radius_km"
                      class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-pink-500"
                    />
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                      <span>1 km</span>
                      <span class="font-semibold text-pink-600">{{ editPrefs.search_radius_km }} km</span>
                      <span>200 km</span>
                    </div>
                  </div>

                  <div class="flex items-center justify-between py-3">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Esconder localização</p>
                      <p class="text-xs text-gray-500">Sua cidade não aparecerá nos perfis</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="editPrefs.hide_location" class="sr-only peer" />
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                    </label>
                  </div>

                  <div class="flex items-center justify-between py-3">
                    <div>
                      <p class="text-sm font-medium text-gray-900">Modo invisível</p>
                      <p class="text-xs text-gray-500">Ninguém verá seu perfil (Premium)</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" v-model="editPrefs.invisible_mode" class="sr-only peer" />
                      <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-pink-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-500"></div>
                    </label>
                  </div>
                </div>

                <button
                  @click="salvarLocalizacao"
                  class="w-full mt-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
                >
                  Salvar
                </button>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal Upgrade (Planos) -->
    <TransitionRoot appear :show="abaAtiva === 'upgrade'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Seu Plano</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <p class="text-sm text-gray-500 mb-2">Seu nível atual:</p>
                <div class="flex items-center gap-3 mb-6 p-3 rounded-xl bg-gray-50">
                  <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm" :class="nivelBadgeClass">
                    {{ nivelLabel?.[0] || '?' }}
                  </div>
                  <div>
                    <p class="text-base font-bold text-gray-900">{{ nivelLabel }}</p>
                    <p class="text-xs text-gray-500">{{ planoDescricao }}</p>
                  </div>
                </div>

                <!-- Tabela comparativa -->
                <p class="text-sm font-semibold text-gray-900 mb-3">Compare os planos</p>
                <div class="space-y-2">
                  <div
                    v-for="plano in planos"
                    :key="plano.nivel"
                    class="rounded-xl border-2 p-4 transition-all"
                    :class="[
                      plano.nivel === authStore.user?.nivel_acesso
                        ? 'border-pink-400 bg-pink-50'
                        : 'border-gray-100 hover:border-pink-200'
                    ]"
                  >
                    <div class="flex items-center justify-between mb-2">
                      <div>
                        <span
                          class="inline-block px-2 py-0.5 rounded-full text-xs font-bold text-white"
                          :class="plano.badgeClass"
                        >
                          {{ plano.nome }}
                        </span>
                        <span class="text-xs text-gray-400 ml-2">{{ plano.preco }}</span>
                      </div>
                      <button
                        v-if="plano.nivel !== authStore.user?.nivel_acesso && plano.nivel > (authStore.user?.nivel_acesso || 0)"
                        class="text-xs font-semibold px-4 py-1.5 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 text-white shadow hover:shadow-md transition-all"
                      >
                        Fazer Upgrade
                      </button>
                      <span
                        v-else-if="plano.nivel === authStore.user?.nivel_acesso"
                        class="text-xs font-semibold text-pink-600"
                      >
                        ✔ Atual
                      </span>
                    </div>
                    <ul class="space-y-1">
                      <li
                        v-for="item in plano.itens"
                        :key="item"
                        class="flex items-start gap-2 text-xs text-gray-600"
                      >
                        <svg class="w-3.5 h-3.5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span v-html="item"></span>
                      </li>
                    </ul>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal Bloqueios -->
    <TransitionRoot appear :show="abaAtiva === 'bloqueios'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Bloqueios</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <p class="text-sm text-gray-500 mb-4">Gerencie usuários e regiões bloqueados.</p>

                <!-- Usuários bloqueados -->
                <div class="mb-6">
                  <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Usuários Bloqueados</h3>
                  </div>
                  <div v-if="!usuariosBloqueados?.length" class="text-center py-6 bg-gray-50 rounded-xl">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    <p class="text-xs text-gray-400">Nenhum usuário bloqueado</p>
                  </div>
                  <div v-else class="space-y-2">
                    <div v-for="u in usuariosBloqueados" :key="u.id" class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-white text-xs font-bold">
                          {{ u.name?.[0]?.toUpperCase() || '?' }}
                        </div>
                        <p class="text-sm font-medium text-gray-700">{{ u.name }}</p>
                      </div>
                      <button @click="desbloquearUsuario(u.id)" class="text-xs text-pink-600 hover:text-pink-700 font-medium">Desbloquear</button>
                    </div>
                  </div>
                </div>

                <!-- Regiões bloqueadas -->
                <div>
                  <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Regiões Bloqueadas</h3>
                  </div>
                  <div v-if="!regioesBloqueadas?.length" class="text-center py-6 bg-gray-50 rounded-xl">
                    <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="text-xs text-gray-400">Nenhuma região bloqueada</p>
                  </div>
                  <div v-else class="space-y-2">
                    <div v-for="r in regioesBloqueadas" :key="r.id" class="flex items-center justify-between bg-gray-50 rounded-lg p-3">
                      <p class="text-sm text-gray-700">{{ r.city?.name || r.state?.name || r.country?.name || 'Região' }}</p>
                      <button @click="desbloquearRegiao(r.id)" class="text-xs text-pink-600 hover:text-pink-700 font-medium">Remover</button>
                    </div>
                  </div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Modal Preferências de Busca -->
    <TransitionRoot appear :show="abaAtiva === 'preferencias'" as="template">
      <Dialog as="div" @close="abaAtiva = null" class="relative z-50">
        <TransitionChild as="template" enter="duration-300 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
          <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" />
        </TransitionChild>
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-end justify-center">
            <TransitionChild as="template" enter="duration-300 ease-out" enter-from="translate-y-full" enter-to="translate-y-0" leave="duration-200 ease-in" leave-from="translate-y-0" leave-to="translate-y-full">
              <DialogPanel class="w-full max-w-lg bg-white rounded-t-3xl shadow-2xl p-6 pb-10 transform transition-all">
                <div class="flex items-center justify-between mb-6">
                  <DialogTitle class="text-lg font-bold text-gray-900">Preferências de Busca</DialogTitle>
                  <button @click="abaAtiva = null" class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>

                <div class="space-y-5">
                  <!-- Faixa etária -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Faixa etária: {{ editPrefs.min_age }} - {{ editPrefs.max_age }} anos
                    </label>
                    <div class="flex gap-4 items-center">
                      <input
                        type="range"
                        min="18"
                        max="85"
                        v-model.number="editPrefs.min_age"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-pink-500"
                      />
                      <span class="text-xs text-gray-500 min-w-8 text-right">{{ editPrefs.min_age }}</span>
                    </div>
                    <div class="flex gap-4 items-center mt-2">
                      <input
                        type="range"
                        min="18"
                        max="85"
                        v-model.number="editPrefs.max_age"
                        class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-500"
                      />
                      <span class="text-xs text-gray-500 min-w-8 text-right">{{ editPrefs.max_age }}</span>
                    </div>
                  </div>

                  <!-- Gênero da busca -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mostrar perfis do gênero</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="g in genderOptions"
                        :key="g.value"
                        @click="editPrefs.gender = g.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editPrefs.gender === g.value ? 'bg-pink-500 text-white border-pink-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ g.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Orientação da busca -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Orientação sexual da busca</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="o in [{value: 'todos', label: 'Todos'}, ...orientationOptions]"
                        :key="o.value"
                        @click="editPrefs.sexual_orientation = o.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editPrefs.sexual_orientation === o.value ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ o.label }}
                      </button>
                    </div>
                  </div>

                  <!-- Propósito -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Propósito</label>
                    <div class="flex flex-wrap gap-2">
                      <button
                        v-for="p in purposeOptions"
                        :key="p.value"
                        @click="editPrefs.purpose = p.value"
                        class="px-4 py-2 rounded-full text-sm font-medium border transition-all"
                        :class="editPrefs.purpose === p.value ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-700 border-gray-200'"
                      >
                        {{ p.label }}
                      </button>
                    </div>
                  </div>
                </div>

                <button
                  @click="salvarPreferenciasBusca"
                  class="w-full mt-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
                >
                  Salvar Preferências
                </button>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import axios from 'axios';
import AppLayout from '../components/AppLayout.vue';
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue';

const router = useRouter();
const authStore = useAuthStore();

// Estado
const profile = ref(null);
const preferencias = ref(null);
const todosHobbies = ref([]);
const abaAtiva = ref(null);
const carregandoHobbies = ref(false);
const salvando = ref(false);
const editandoFotos = ref(false);
const photoIndex = ref(0);

// Dados de edição
const editProfile = reactive({
  gender: '',
  gender_identity: '',
  sexual_orientation: '',
});

const editPrefs = reactive({
  search_radius_km: 50,
  hide_location: false,
  invisible_mode: false,
  min_age: 18,
  max_age: 85,
  gender: 'todos',
  sexual_orientation: 'todos',
  purpose: 'all',
});

// Opções
const genderOptions = [
  { value: 'male', label: 'Masculino' },
  { value: 'female', label: 'Feminino' },
  { value: 'non_binary', label: 'Não-binário' },
];

const genderIdentityOptions = [
  { value: 'male', label: 'Masculino' },
  { value: 'female', label: 'Feminino' },
  { value: 'non_binary', label: 'Não-binário' },
  { value: 'other', label: 'Outro' },
];

const orientationOptions = [
  { value: 'heterosexual', label: 'Heterossexual' },
  { value: 'homosexual', label: 'Homossexual' },
  { value: 'bisexual', label: 'Bissexual' },
  { value: 'pansexual', label: 'Pansexual' },
];

const purposeOptions = [
  { value: 'friendship', label: 'Amizade' },
  { value: 'casual', label: 'Casual' },
  { value: 'all', label: 'Todos' },
];

// Nível do usuário
const nivelLabel = computed(() => {
  const level = authStore.user?.nivel_acesso;
  const labels = {
    0: 'Free',
    1: 'Moderador',
    2: 'Plus',
    3: 'Premium',
    4: 'Co-Founder',
    5: 'Elite',
  };
  return labels[level] || 'Usuário';
});

const nivelBadgeClass = computed(() => {
  const level = authStore.user?.nivel_acesso;
  const colors = {
    0: 'bg-gray-500',
    1: 'bg-blue-500',
    2: 'bg-green-500',
    3: 'bg-purple-500',
    4: 'bg-yellow-500',
    5: 'bg-pink-500',
  };
  return colors[level] || 'bg-gray-500';
});

// Helpers
const genderLabel = (g) => {
  const map = { male: 'Masculino', female: 'Feminino', non_binary: 'Não-binário' };
  return map[g] || g;
};

const orientationLabel = (o) => {
  const map = { heterosexual: 'Heterossexual', homosexual: 'Homossexual', bisexual: 'Bissexual', pansexual: 'Pansexual' };
  return map[o] || o;
};

const purposeLabel = (p) => {
  const map = { friendship: 'Amizade', casual: 'Casual', all: 'Todos' };
  return map[p] || p;
};

const planos = computed(() => [
  {
    nivel: 0,
    nome: 'Free',
    preco: 'Grátis',
    badgeClass: 'bg-gray-500',
    itens: [
      'Até <b>70 curtidas</b> por dia',
      'Filtrar por idade, sexo e orientação',
      'Enviar mensagens <b>após match</b>',
      'Filtrar região e raio de cidades',
    ],
  },
  {
    nivel: 2,
    nome: 'Plus',
    preco: 'R$ 15,00/mês',
    badgeClass: 'bg-green-500',
    itens: [
      'Curtidas <b>ilimitadas</b>',
      'Filtrar por idade, sexo e orientação',
      'Até <b>10 mensagens/dia</b> sem match',
      'Filtrar região com raio <b>0-200km</b>',
      'Bloquear regiões que não quer ver',
      '<b>Esconder localização</b>',
    ],
  },
  {
    nivel: 3,
    nome: 'Premium',
    preco: 'R$ 19,90/mês',
    badgeClass: 'bg-purple-500',
    itens: [
      'Curtidas <b>ilimitadas</b>',
      'Filtrar por idade, sexo e orientação',
      'Mensagens <b>limitadas</b> sem match',
      'Filtrar região com raio <b>0-200km</b>',
      'Bloquear regiões que não quer ver',
      '<b>Esconder localização</b>',
      'Ver quem <b>curtiu seu perfil</b>',
      '<b>Modo invisível</b> — ninguém te vê',
    ],
  },
  {
    nivel: 4,
    nome: 'Co-Founder',
    preco: 'Sob consulta',
    badgeClass: 'bg-yellow-500',
    itens: [
      'Todos os benefícios do Premium',
      '<b>Solicitações diretas</b> para outros perfis',
      'Suporte prioritário',
    ],
  },
  {
    nivel: 5,
    nome: 'Elite',
    preco: 'Sob consulta',
    badgeClass: 'bg-pink-500',
    itens: [
      'Todos os benefícios do Co-Founder',
      'Acesso total a todas funcionalidades',
      'Suporte VIP 24h',
    ],
  },
]);

const planoDescricao = computed(() => {
  const level = authStore.user?.nivel_acesso;
  const descs = {
    0: 'Usuário gratuito — aproveite o básico do app',
    1: 'Moderador — pode bloquear acesso de outros usuários',
    2: 'Usuário Plus — curtidas ilimitadas e mensagens sem match',
    3: 'Usuário Premium — modo invisível e quem curtiu você',
    4: 'Co-Founder — solicitações diretas',
    5: 'Elite — acesso VIP completo',
  };
  return descs[level] || 'Plano personalizado';
});

const usuariosBloqueados = ref([]);
const regioesBloqueadas = ref([]);

const hobbySelecionado = (id) => {
  return profile.value?.hobbies?.some(h => h.id === id || h.pivot?.hobby_id === id);
};

async function carregarBloqueios() {
  try {
    const [usersRes, regioesRes] = await Promise.all([
      axios.get('/api/blocks/users'),
      axios.get('/api/blocks/regions'),
    ]);
    usuariosBloqueados.value = usersRes.data.data || usersRes.data || [];
    regioesBloqueadas.value = regioesRes.data.data || regioesRes.data || [];
  } catch (e) {
    console.error('Erro ao carregar bloqueios:', e);
  }
}

async function desbloquearUsuario(userId) {
  try {
    await axios.delete(`/api/blocks/users/${userId}`);
    usuariosBloqueados.value = usuariosBloqueados.value.filter(u => u.id !== userId);
  } catch (e) {
    console.error('Erro ao desbloquear usuário:', e);
  }
}

async function desbloquearRegiao(regionId) {
  try {
    await axios.delete(`/api/blocks/regions/${regionId}`);
    regioesBloqueadas.value = regioesBloqueadas.value.filter(r => r.id !== regionId);
  } catch (e) {
    console.error('Erro ao desbloquear região:', e);
  }
}

// Carregar dados
async function carregarPerfil() {
  try {
    const res = await axios.get('/api/profile');
    profile.value = res.data.profile || res.data;
    preferencias.value = profile.value?.preference || profile.value?.preferences || null;

    // Preencher dados de edição
    if (profile.value) {
      editProfile.gender = profile.value.gender || '';
      editProfile.gender_identity = profile.value.gender_identity || '';
      editProfile.sexual_orientation = profile.value.sexual_orientation || '';
    }

    if (preferencias.value) {
      editPrefs.search_radius_km = preferencias.value.search_radius_km || 50;
      editPrefs.hide_location = preferencias.value.hide_location || false;
      editPrefs.invisible_mode = preferencias.value.invisible_mode || false;
      editPrefs.min_age = preferencias.value.min_age || 18;
      editPrefs.max_age = preferencias.value.max_age || 85;
      editPrefs.gender = preferencias.value.gender || 'todos';
      editPrefs.sexual_orientation = preferencias.value.sexual_orientation || 'todos';
      editPrefs.purpose = preferencias.value.purpose || 'all';
    }
  } catch (error) {
    console.error('Erro ao carregar perfil:', error);
  }
}

async function carregarHobbies() {
  try {
    const res = await axios.get('/api/hobbies');
    todosHobbies.value = res.data.data || res.data;
  } catch (error) {
    console.error('Erro ao carregar hobbies:', error);
  }
}

function toggleHobby(id) {
  if (!profile.value) return;
  const idx = profile.value.hobbies?.findIndex(h => h.id === id || h.pivot?.hobby_id === id);
  if (idx >= 0) {
    profile.value.hobbies.splice(idx, 1);
  } else {
    profile.value.hobbies.push({ id, pivot: { hobby_id: id } });
  }
}

async function salvarHobbies() {
  salvando.value = true;
  try {
    const hobbyIds = profile.value?.hobbies?.map(h => h.id || h.pivot?.hobby_id) || [];
    await axios.put('/api/profile/hobbies', { hobbies: hobbyIds });
    abaAtiva.value = null;
  } catch (error) {
    console.error('Erro ao salvar hobbies:', error);
  } finally {
    salvando.value = false;
  }
}

async function salvarSexualidade() {
  try {
    await axios.put('/api/profile', {
      gender: editProfile.gender,
      gender_identity: editProfile.gender_identity,
      sexual_orientation: editProfile.sexual_orientation,
    });
    profile.value.gender = editProfile.gender;
    profile.value.gender_identity = editProfile.gender_identity;
    profile.value.sexual_orientation = editProfile.sexual_orientation;
    abaAtiva.value = null;
  } catch (error) {
    console.error('Erro ao salvar sexualidade:', error);
  }
}

async function salvarLocalizacao() {
  try {
    await axios.put('/api/profile/preferences', {
      search_radius_km: editPrefs.search_radius_km,
      hide_location: editPrefs.hide_location,
      invisible_mode: editPrefs.invisible_mode,
    });
    abaAtiva.value = null;
  } catch (error) {
    console.error('Erro ao salvar localização:', error);
  }
}

async function salvarPreferenciasBusca() {
  try {
    await axios.put('/api/profile/preferences', {
      min_age: editPrefs.min_age,
      max_age: editPrefs.max_age,
      gender: editPrefs.gender,
      sexual_orientation: editPrefs.sexual_orientation,
      purpose: editPrefs.purpose,
    });
    abaAtiva.value = null;
  } catch (error) {
    console.error('Erro ao salvar preferências:', error);
  }
}

onMounted(async () => {
  await carregarPerfil();
  await carregarHobbies();
  await carregarBloqueios();
});
</script>