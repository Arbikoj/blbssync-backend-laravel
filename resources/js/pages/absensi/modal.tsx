import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { AttendanceFormData, AttendanceTypes } from '@/interface/Attendances';
import { ScheduleTypes } from '@/interface/Schedules';
import { Teacher } from '@/interface/Teacher';
import api from '@/lib/api';
import { useEffect, useState } from 'react';

type ModalScheduleProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    mode: 'add' | 'edit';
    initialData?: AttendanceFormData;
    onSuccess?: () => void;
};

export default function ModalSchedule({ open, onOpenChange, mode, initialData, onSuccess }: ModalScheduleProps) {
    const [userType, setUserType] = useState('');
    const [teacherId, setTeacherId] = useState<number | null>(null);
    const [schedulesId, setSchedulesId] = useState<number | null>(null);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState<{ [key: string]: string[] }>({});

    const [schedules, setSchedules] = useState<ScheduleTypes[]>([]);
    const [teachers, setTeachers] = useState<Teacher[]>([]);

    const fetchSchedules = async () => {
        try {
            const res = await api.get('/schedules');
            setSchedules(res.data.data);
        } catch (error) {
            console.error('Gagal load schedules:', error);
        }
    };

    const fetchTeachers = async () => {
        try {
            const res = await api.get('/teachers');
            setTeachers(res.data.data);
        } catch (error) {
            console.error('Gagal load teachers:', error);
        }
    };

    // === useEffects ===
    useEffect(() => {
        fetchSchedules();
        fetchTeachers();
    }, []);

    useEffect(() => {
        if (initialData) {
            setTeacherId(initialData.teacher_id);
            setSchedulesId(initialData.schedule_id);
            setUserType(initialData.user_type);
        } else {
            setTeacherId(null);
            setSchedulesId(null);
            setUserType('');
        }
        setErrors({});
    }, [initialData, open]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        const payload: AttendanceTypes = {
            id: initialData?.id ?? 0,
            teacher_id: teacherId ?? 0,
            schedule_id: schedulesId ?? 0,
            user_type: userType ?? '',
        };

        try {
            if (mode === 'add') {
                await api.post('/attendances', payload);
            } else {
                await api.put(`/attendances/${payload.id}`, payload);
            }

            if (onSuccess) onSuccess();
            onOpenChange(false);
        } catch (error: any) {
            if (error.response?.status === 422) {
                setErrors(error.response.data.message);
            } else {
                console.error('Gagal menyimpan Absensi:', error);
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>{mode === 'add' ? 'Tambah' : 'Edit'} Absensi</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <Label htmlFor="teacher">Guru</Label>
                        {teachers.length > 0 && (
                            <Select value={teacherId !== null ? teacherId.toString() : ''} onValueChange={(val) => setTeacherId(Number(val))}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Pilih Guru" />
                                </SelectTrigger>
                                <SelectContent>
                                    {teachers.map((teacher) => (
                                        <SelectItem key={teacher.id} value={teacher.id.toString()}>
                                            {teacher.name}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        )}
                        {errors.teacher_id && <p className="mt-1 text-sm text-red-600">{errors.teacher_id[0]}</p>}
                    </div>

                    <div>
                        <Label htmlFor="schedule">Jadwal</Label>
                        {schedules.length > 0 && (
                            <Select value={schedulesId !== null ? schedulesId.toString() : ''} onValueChange={(val) => setSchedulesId(Number(val))}>
                                <SelectTrigger className="w-full">
                                    <SelectValue placeholder="Pilih Kelas" />
                                </SelectTrigger>
                                <SelectContent>
                                    {schedules.map((group) => (
                                        <SelectItem key={group.id} value={group.id.toString()}>
                                            {group.day} jam ke-{group.lesson.state}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        )}
                        {errors.schedule_id && <p className="mt-1 text-sm text-red-600">{errors.schedule_id[0]}</p>}
                    </div>

                    <div>
                        <Label htmlFor="schedule">Tipe User</Label>
                        <Select value={userType !== null ? userType.toString() : ''} onValueChange={(val) => setUserType(val)}>
                            <SelectTrigger className="w-full">
                                <SelectValue placeholder="Pilih User" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="teacher">Teacher</SelectItem>
                                <SelectItem value="student">Student</SelectItem>
                            </SelectContent>
                        </Select>
                        {errors.userType && <p className="mt-1 text-sm text-red-600">{errors.userType[0]}</p>}
                    </div>

                    <div className="flex justify-end">
                        <Button type="submit" disabled={loading}>
                            {loading ? 'Menyimpan...' : mode === 'add' ? 'Simpan' : 'Update'}
                        </Button>
                    </div>
                </form>
            </DialogContent>
        </Dialog>
    );
}
