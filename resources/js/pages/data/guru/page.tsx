import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';
import { Teacher, columns as baseColumns } from './column';
import { DeleteDialog } from './delete';
import ModalGuru, { GuruFormData } from './modal';
import { DataTable } from '../../../components/tanstack-table';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Data Guru', href: '/data/guru' }];

const PageGuru = () => {
    const [data, setData] = useState<Teacher[]>([]);
    const [loading, setLoading] = useState(true);

    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'add' | 'edit'>('add');
    const [editData, setEditData] = useState<GuruFormData | undefined>(undefined);

    const fetchData = () => {
        setLoading(true);
        axios
            .get('/api/teachers')
            .then((res) => setData(res.data.data))
            .catch((err) => console.error(err))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        fetchData();
    }, []);

    const handleAdd = () => {
        setModalMode('add');
        setEditData(undefined);
        setModalOpen(true);
    };

    const handleEdit = (teacher: Teacher) => {
        setModalMode('edit');
        setEditData(teacher);
        setModalOpen(true);
    };

    const handleSubmit = (formData: GuruFormData) => {
        if (modalMode === 'add') {
            axios
                .post('/api/teachers', formData)
                .then(fetchData)
                .finally(() => setModalOpen(false));
        } else {
            axios
                .put(`/api/teachers/${formData.id}`, formData)
                .then(fetchData)
                .finally(() => setModalOpen(false));
        }
    };

    const columns = [
        ...baseColumns,
        {
            id: 'actions',
            header: 'Aksi',
            cell: ({ row }: any) => (
                <div className="flex items-center space-x-2">
                    <button onClick={() => handleEdit(row.original)} className="text-sm text-blue-600 hover:underline">
                        Edit
                    </button>
                    <DeleteDialog teacherId={row.original.id} teacherName={row.original.name} onDeleted={fetchData} />
                </div>
            ),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Guru" />
            <ModalGuru
                open={modalOpen}
                onOpenChange={(open) => {
                    setModalOpen(open);
                    if (!open) fetchData();
                }}
                mode={modalMode}
                initialData={editData}
                onSubmit={handleSubmit}
            />
            <div className="flex items-center justify-between px-5 pt-2">{handleAdd && <Button onClick={handleAdd}>Tambah</Button>}</div>

            <DataTable data={data} columns={columns} isLoading={loading} />
        </AppLayout>
    );
};

export default PageGuru;
