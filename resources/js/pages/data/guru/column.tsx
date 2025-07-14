import { ColumnDef } from '@tanstack/react-table';

export type Teacher = {
    id: number;
    name: string;
    code: number;
};

export const columns: ColumnDef<Teacher>[] = [
    {
        id: 'rowNumber',
        header: '#',
        cell: ({ row }) => row.index + 1,
    },
    {
        accessorKey: 'name',
        header: 'Nama',
    },
    {
        accessorKey: 'code',
        header: 'Kode',
    },
];
