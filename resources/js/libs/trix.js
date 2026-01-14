import Trix from 'trix';
import axios from 'axios';

window.Trix = Trix;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

document.addEventListener('trix-attachment-add', async (event) => {
    const attachment = event.detail.attachment;
    const file = attachment.file;

    if (! file) {
        return;
    }

    const formData = new FormData();
    formData.append('attachment', file);

    try {
        const response = await axios.post('/admin/attachments', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            },
            onUploadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    const progress = Math.round((progressEvent.loaded / progressEvent.total) * 100);
                    attachment.setUploadProgress(progress);
                }
            },
        });

        const url = response.data.url;

        attachment.setAttributes({
            url,
            href: url,
        });
    } catch (error) {
        console.error('Attachment upload failed:', error);
        attachment.remove();
    }
});

export default Trix;
