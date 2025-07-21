import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import axios from 'axios';
import { Pencil } from 'lucide-react';
import { useEffect, useState } from 'react';
import { DataTable } from '../../../components/tanstack-table';
import { Teacher, columns as baseColumns } from './column';
import { DeleteDialog } from './delete';
import ModalGuru, { GuruFormData } from './modal';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Data Guru', href: '/data/guru' }];

const PageGuru = () => {
    const [data, setData] = useState<Teacher[]>([]);
    const [loading, setLoading] = useState(true);
    const [totalItems, setTotalItems] = useState(0);
    const [pagination, setPagination] = useState({
        pageIndex: 0,
        pageSize: 10,
    });

    const [modalOpen, setModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState<'add' | 'edit'>('add');
    const [editData, setEditData] = useState<GuruFormData | undefined>(undefined);

    const fetchData = () => {
        setLoading(true);
        axios
            .get('/api/teachers', {
                params: {
                    page: pagination.pageIndex + 1,
                    per_page: pagination.pageSize,
                },
            })
            .then((res) => {
                const { data, meta } = res.data;
                setData(data);
                setTotalItems(meta.total);
            })
            .catch((err) => console.error(err))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        fetchData();
    }, [pagination]);

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
                    <Button variant="secondary" size="icon" className="text-blue-600 hover:text-blue-700" onClick={() => handleEdit(row.original)}>
                        <Pencil className="h-4 w-4" />
                    </Button>
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

            <DataTable
                data={data}
                columns={columns}
                isLoading={loading}
                pagination={pagination}
                onPaginationChange={setPagination}
                pageCount={Math.ceil(totalItems / pagination.pageSize)}
            />
        </AppLayout>
    );
};

export default PageGuru;
