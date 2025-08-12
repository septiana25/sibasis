"use strict";

$(document).ready(function() {
    //configuration filepond
    const fCover = document.querySelector('input[id="f_cover"]');
    const fBab1 = document.querySelector('input[id="f_bab1"]');
    const fBab2 = document.querySelector('input[id="f_bab2"]');
    const fBab3 = document.querySelector('input[id="f_bab3"]');
    const fBab4 = document.querySelector('input[id="f_bab4"]');
    const fBab5 = document.querySelector('input[id="f_bab5"]');
    const fDafpus = document.querySelector('input[id="f_dafpus"]'); 
    const fLampiran = document.querySelector('input[id="f_lampiran"]'); 
    const fFulltext = document.querySelector('input[id="f_fulltext"]'); 

    FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginFileValidateSize);

    // Create a FilePond instance
    const F_cover = FilePond.create((fCover), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah Cover s.d. Daftar Isi'
    });
    const F_bab1 = FilePond.create((fBab1), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah BAB 1'
    });
    const F_bab2 = FilePond.create((fBab2), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah BAB 2'
    });
    const F_bab3 = FilePond.create((fBab3), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah BAB 3'
    });
    const F_bab4 = FilePond.create((fBab4), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah BAB 4'
    });
    const F_bab5 = FilePond.create((fBab5), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah BAB 5'
    });
    const F_dafpus = FilePond.create((fDafpus), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah Daftar Pustaka'
    });
    const F_lampiran = FilePond.create((fLampiran), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah Lampiran'
    });
    const F_fulltext = FilePond.create((fFulltext), {
        acceptedFileTypes: ['application/pdf'],
        maxFileSize: '10MB',
        labelIdle: 'Unggah Fulltext'
    });

    // //tujuan filepond pindah ke blade ajuan
    // FilePond.setOptions({
    //     server: {
    //         process: "{{ route('uploadfile') }}", //upload
    //         revert: "{{ route('hapusfile') }}", //cancel
    //         headers: {
    //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
    //         }
    //     }
    // });
});